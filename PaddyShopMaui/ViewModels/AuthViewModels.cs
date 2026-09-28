using System.Text.RegularExpressions;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class LoginViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    [ObservableProperty] private string username = "";
    [ObservableProperty] private string password = "";
    [ObservableProperty] private bool showPassword;

    [RelayCommand]
    private async Task LoginAsync()
    {
        if (string.IsNullOrWhiteSpace(Username)) { ErrorMessage = "Vui lòng nhập tài khoản!"; return; }
        if (string.IsNullOrEmpty(Password)) { ErrorMessage = "Vui lòng nhập mật khẩu!"; return; }
        var ok = await RunAsync(() => Api.LoginAsync(Username, Password), showErrorBox: true);
        if (ok)
        {
            Password = "";
            Notifier.Toast("Đăng nhập thành công");
            await Routes.BackAsync();
        }
    }

    [RelayCommand] private void TogglePassword() => ShowPassword = !ShowPassword;
    [RelayCommand] private Task Register() => Shell.Current.GoToAsync($"../{Routes.Register}");
    [RelayCommand] private Task Forgot() => Routes.GoAsync(Routes.ForgotPassword);
    [RelayCommand] private Task Server() => Routes.GoAsync(Routes.Server);
}

public partial class RegisterViewModel : BaseViewModel
{
    private CancellationTokenSource? _checkCts;

    public ProfileFormViewModel Form { get; }

    [ObservableProperty] private string username = "";
    [ObservableProperty] private string password = "";
    [ObservableProperty] private string confirmPassword = "";
    [ObservableProperty] private string usernameHint = "Chữ không dấu, số và dấu _";
    [ObservableProperty] private Color usernameHintColor = Colors.Gray;
    private bool? _usernameAvailable;

    public RegisterViewModel(ShopApi api, SessionService session) : base(api, session)
    {
        Form = new ProfileFormViewModel(api);
    }

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Form.Provinces.Count == 0) await Form.InitializeAsync();
    }

    /// <summary>Kiểm tra tên đăng nhập đã tồn tại chưa (giống get_register.php), chờ ngừng gõ 0,5 giây</summary>
    partial void OnUsernameChanged(string value)
    {
        var cleaned = Regex.Replace(value, "[^A-Za-z0-9_]", "");
        if (cleaned != value) { Username = cleaned; return; }
        _usernameAvailable = null;
        _checkCts?.Cancel();
        if (value.Length < 3)
        {
            UsernameHint = "Chữ không dấu, số và dấu _ (3-20 ký tự)";
            UsernameHintColor = Colors.Gray;
            return;
        }
        var cts = _checkCts = new CancellationTokenSource();
        _ = CheckUsernameAsync(value, cts.Token);
    }

    private async Task CheckUsernameAsync(string value, CancellationToken token)
    {
        try
        {
            await Task.Delay(500, token);
            var available = await Api.IsUsernameAvailableAsync(value);
            if (token.IsCancellationRequested) return;
            _usernameAvailable = available;
            UsernameHint = available ? "✓ Có thể sử dụng" : "✗ Tên người dùng đã được sử dụng";
            UsernameHintColor = available ? Color.FromArgb("#2E7D32") : Color.FromArgb("#CC3333");
        }
        catch (TaskCanceledException) { }
        catch (ApiException) { }
    }

    [RelayCommand]
    private async Task RegisterAsync()
    {
        var msg = Form.Validate();
        if (msg == null)
        {
            if (!Regex.IsMatch(Username, "^[A-Za-z0-9_]{3,20}$")) msg = "Tên đăng nhập 3-20 ký tự, chỉ gồm chữ không dấu, số và dấu _";
            else if (_usernameAvailable == false) msg = "Tên người dùng đã được sử dụng";
            else if (Password.Length is < 8 or > 20) msg = "Mật khẩu phải từ 8 đến 20 ký tự";
            else if (Password != ConfirmPassword) msg = "Mật khẩu xác nhận không khớp";
        }
        if (msg != null) { ErrorMessage = msg; return; }

        var ok = await RunAsync(() => Api.RegisterAsync(Form.ToRequest(Username, Password)), showErrorBox: true);
        if (ok)
        {
            Notifier.Toast("Đăng ký thành công, chào mừng bạn!");
            await Routes.BackAsync();
        }
    }

    [RelayCommand] private Task Login() => Shell.Current.GoToAsync($"../{Routes.Login}");
}

public partial class ForgotPasswordViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    [ObservableProperty] private string email = "";
    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(IsEmailStep), nameof(Instruction))]
    private bool codeSent;
    [ObservableProperty] private string code = "";
    [ObservableProperty] private string newPassword = "";
    [ObservableProperty] private string confirmPassword = "";
    [ObservableProperty] private string? infoMessage;

    public bool IsEmailStep => !CodeSent;
    public string Instruction => CodeSent
        ? $"Nhập mã 6 số đã gửi tới {Email} và mật khẩu mới."
        : "Vui lòng điền email tài khoản, chúng tôi sẽ gửi mã xác nhận để đặt lại mật khẩu.";

    [RelayCommand]
    private async Task SendCodeAsync()
    {
        if (!Regex.IsMatch(Email.Trim(), @"^[^@\s]+@[^@\s]+\.[^@\s]+$")) { ErrorMessage = "Email không hợp lệ"; return; }
        string? msg = null;
        if (await RunAsync(async () => msg = await Api.ForgotPasswordAsync(Email), showErrorBox: true))
        {
            InfoMessage = msg;
            CodeSent = true;
        }
    }

    [RelayCommand]
    private async Task ResetAsync()
    {
        string? err = null;
        if (!Regex.IsMatch(Code.Trim(), @"^\d{6}$")) err = "Mã xác nhận gồm 6 chữ số";
        else if (NewPassword.Length is < 8 or > 20) err = "Mật khẩu phải có ít nhất 8 ký tự và ít hơn 20 ký tự";
        else if (NewPassword != ConfirmPassword) err = "Mật khẩu xác nhận không khớp.";
        if (err != null) { ErrorMessage = err; return; }

        string? msg = null;
        if (await RunAsync(async () => msg = await Api.ResetPasswordAsync(Email, Code, NewPassword), showErrorBox: true))
        {
            await Notifier.AlertAsync("Thành công", msg ?? "Đặt lại mật khẩu thành công, vui lòng đăng nhập");
            await Routes.BackAsync();
        }
    }

    [RelayCommand]
    private void ChangeEmail()
    {
        CodeSent = false;
        Code = "";
        InfoMessage = null;
        ErrorMessage = null;
    }
}

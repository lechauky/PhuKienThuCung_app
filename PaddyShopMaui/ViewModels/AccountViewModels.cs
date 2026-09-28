using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class AccountViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    public string VersionText => "Phiên bản " + AppInfo.Current.VersionString;

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Session.IsLoggedIn)
        {
            try { await Api.RefreshMeAsync(); } catch (ApiException) { }
            try { await Api.NotificationsAsync(); } catch (ApiException) { }
        }
    }

    [RelayCommand] private Task Register() => Routes.GoAsync(Routes.Register);
    [RelayCommand] private Task EditProfile() => Routes.GoAsync(Routes.EditProfile);
    [RelayCommand] private Task ChangePassword() => Routes.GoAsync(Routes.ChangePassword);
    [RelayCommand] private Task Orders() => Routes.GoTabAsync(Routes.Orders);
    [RelayCommand] private Task Notifications() => Routes.GoAsync(Routes.Notifications);
    [RelayCommand] private Task Contact() => Routes.GoAsync(Routes.Contact);
    [RelayCommand] private Task Server() => Routes.GoAsync(Routes.Server);

    [RelayCommand]
    private async Task LogoutAsync()
    {
        if (!await Notifier.ConfirmAsync("Đăng xuất", "Bạn có chắc muốn đăng xuất?", "Đăng xuất")) return;
        Api.Logout();
        Notifier.Toast("Đã đăng xuất");
    }
}

public partial class EditProfileViewModel : BaseViewModel
{
    private bool _initialized;
    public ProfileFormViewModel Form { get; }

    public EditProfileViewModel(ShopApi api, SessionService session) : base(api, session)
    {
        Form = new ProfileFormViewModel(api);
    }

    public string UsernameText => Session.Customer?.Username ?? "";

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (!Session.IsLoggedIn) { await Routes.BackAsync(); return; }
        if (!_initialized)
        {
            _initialized = true;
            OnPropertyChanged(nameof(UsernameText));
            await Form.InitializeAsync(Session.Customer);
        }
    }

    [RelayCommand]
    private async Task SaveAsync()
    {
        var msg = Form.Validate();
        if (msg != null) { ErrorMessage = msg; return; }
        if (await RunAsync(() => Api.UpdateProfileAsync(Form.ToRequest()), showErrorBox: true))
        {
            Notifier.Toast("Cập nhật thông tin thành công");
            await Routes.BackAsync();
        }
    }
}

public partial class ChangePasswordViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    [ObservableProperty] private string oldPassword = "";
    [ObservableProperty] private string newPassword = "";
    [ObservableProperty] private string confirmPassword = "";

    [RelayCommand]
    private async Task SaveAsync()
    {
        string? err = null;
        if (string.IsNullOrEmpty(OldPassword)) err = "Vui lòng nhập mật khẩu hiện tại";
        else if (NewPassword.Length is < 8 or > 20) err = "Mật khẩu mới phải từ 8 đến 20 ký tự";
        else if (NewPassword != ConfirmPassword) err = "Mật khẩu xác nhận không khớp.";
        if (err != null) { ErrorMessage = err; return; }

        string? msg = null;
        if (await RunAsync(async () => msg = await Api.ChangePasswordAsync(OldPassword, NewPassword), showErrorBox: true))
        {
            Notifier.Toast(msg ?? "Đổi mật khẩu thành công");
            await Routes.BackAsync();
        }
    }
}

public partial class ContactViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasInfo))]
    private ShopInfo? info;

    public bool HasInfo => Info != null;

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Info == null) await LoadAsync();
    }

    [RelayCommand]
    private async Task LoadAsync() => await RunAsync(async () => Info = await Api.ShopInfoAsync(), showErrorBox: true);

    private static async Task OpenUri(string uri)
    {
        try
        {
            if (!await Launcher.Default.TryOpenAsync(uri)) Notifier.Toast("Không tìm thấy ứng dụng phù hợp trên máy");
        }
        catch (Exception)
        {
            Notifier.Toast("Không mở được ứng dụng");
        }
    }

    [RelayCommand] private Task Call() => Info == null ? Task.CompletedTask : OpenUri("tel:" + Info.Hotline);
    [RelayCommand] private Task SendEmail() => Info == null ? Task.CompletedTask : OpenUri("mailto:" + Info.Email);
    [RelayCommand] private Task OpenMap() => Info == null ? Task.CompletedTask : OpenUri("geo:0,0?q=" + Uri.EscapeDataString(Info.Address));
}

public partial class ServerSettingsViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    [ObservableProperty] private string url = "";
    [ObservableProperty] private string? resultMessage;
    [ObservableProperty] private Color resultColor = Colors.Gray;

    public string DefaultUrl => SessionService.DefaultBaseUrl;

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (string.IsNullOrEmpty(Url)) Url = Session.BaseUrl;
    }

    /// <summary>Thử gọi API với địa chỉ mới, sau đó trả lại địa chỉ cũ nếu chưa bấm Lưu</summary>
    [RelayCommand]
    private async Task TestAsync()
    {
        if (string.IsNullOrWhiteSpace(Url)) return;
        var previous = Session.BaseUrl;
        Session.SaveBaseUrl(Url);
        try
        {
            IsBusy = true;
            var cats = await Api.CategoriesAsync();
            ResultMessage = $"✓ Kết nối thành công ({cats.Count} loại sản phẩm)";
            ResultColor = Color.FromArgb("#2E7D32");
        }
        catch (ApiException e)
        {
            ResultMessage = e.Message;
            ResultColor = Color.FromArgb("#CC3333");
        }
        finally
        {
            IsBusy = false;
            Session.SaveBaseUrl(previous);
        }
    }

    [RelayCommand]
    private async Task SaveAsync()
    {
        if (string.IsNullOrWhiteSpace(Url)) return;
        Session.SaveBaseUrl(Url);
        Notifier.Toast("Đã lưu địa chỉ máy chủ");
        await Routes.BackAsync();
    }

    [RelayCommand]
    private void ResetDefault()
    {
        Url = SessionService.DefaultBaseUrl;
        ResultMessage = null;
    }
}

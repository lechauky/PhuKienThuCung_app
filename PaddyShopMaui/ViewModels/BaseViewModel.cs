using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public abstract partial class BaseViewModel(ShopApi api, SessionService session) : ObservableObject
{
    protected ShopApi Api { get; } = api;
    public SessionService Session { get; } = session;

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(IsNotBusy))]
    private bool isBusy;

    /// <summary>Lỗi khi tải dữ liệu chính của trang (hiện khung lỗi + nút Thử lại)</summary>
    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasError))]
    private string? errorMessage;

    public bool IsNotBusy => !IsBusy;
    public bool HasError => !string.IsNullOrEmpty(ErrorMessage);

    /// <summary>Gọi khi trang hiện ra (OnAppearing)</summary>
    public virtual Task OnAppearingAsync() => Session.EnsureLoadedAsync();

    /// <summary>Chạy tác vụ gọi API, bắt lỗi ApiException. showErrorBox = true: lỗi hiện ở khung lỗi; false: hiện Toast</summary>
    protected async Task<bool> RunAsync(Func<Task> action, bool showErrorBox = false, bool useBusy = true)
    {
        if (useBusy && IsBusy) return false;
        if (useBusy) IsBusy = true;
        if (showErrorBox) ErrorMessage = null;
        try
        {
            await action();
            return true;
        }
        catch (ApiException e)
        {
            if (showErrorBox) ErrorMessage = e.Message;
            else Notifier.Toast(e.Message);
            return false;
        }
        catch (Exception e)
        {
            var msg = "Có lỗi xảy ra: " + e.Message;
            if (showErrorBox) ErrorMessage = msg;
            else Notifier.Toast(msg);
            return false;
        }
        finally
        {
            if (useBusy) IsBusy = false;
        }
    }

    /// <summary>Thêm vào giỏ dùng chung; chưa đăng nhập thì mở trang đăng nhập (giống website)</summary>
    protected async Task AddToCartCoreAsync(Product? p, int quantity = 1)
    {
        if (p == null) return;
        if (!Session.IsLoggedIn)
        {
            Notifier.Toast("Yêu cầu đăng nhập!");
            await Routes.GoAsync(Routes.Login);
            return;
        }
        await RunAsync(async () => Notifier.Toast(await Api.AddToCartAsync(p.Id, quantity)), useBusy: false);
    }

    [RelayCommand]
    protected Task OpenProduct(Product? p) => p == null ? Task.CompletedTask : Routes.OpenProductAsync(p.Id);

    [RelayCommand]
    protected Task AddToCart(Product? p) => AddToCartCoreAsync(p);

    [RelayCommand]
    protected Task GoLogin() => Routes.GoAsync(Routes.Login);

    [RelayCommand]
    protected Task GoBack() => Routes.BackAsync();
}

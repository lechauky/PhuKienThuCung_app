using System.ComponentModel;
using PaddyShop.Services;
using PaddyShop.Views;

namespace PaddyShop;

public partial class AppShell : Shell
{
    private readonly SessionService _session;

    public AppShell(SessionService session)
    {
        InitializeComponent();
        _session = session;

        // Các trang con (mở bằng Shell.Current.GoToAsync("<route>?..."))
        Routing.RegisterRoute(Routes.ProductDetail, typeof(ProductDetailPage));
        Routing.RegisterRoute(Routes.Checkout, typeof(CheckoutPage));
        Routing.RegisterRoute(Routes.OrderSuccess, typeof(OrderSuccessPage));
        Routing.RegisterRoute(Routes.OrderDetail, typeof(OrderDetailPage));
        Routing.RegisterRoute(Routes.Notifications, typeof(NotificationsPage));
        Routing.RegisterRoute(Routes.Login, typeof(LoginPage));
        Routing.RegisterRoute(Routes.Register, typeof(RegisterPage));
        Routing.RegisterRoute(Routes.ForgotPassword, typeof(ForgotPasswordPage));
        Routing.RegisterRoute(Routes.EditProfile, typeof(EditProfilePage));
        Routing.RegisterRoute(Routes.ChangePassword, typeof(ChangePasswordPage));
        Routing.RegisterRoute(Routes.Contact, typeof(ContactPage));
        Routing.RegisterRoute(Routes.Server, typeof(ServerSettingsPage));

        // Tab bar của Shell không có badge -> hiện số loại sản phẩm ngay trên tên tab "Giỏ hàng"
        _session.PropertyChanged += OnSessionChanged;
    }

    private void OnSessionChanged(object? sender, PropertyChangedEventArgs e)
    {
        if (e.PropertyName != nameof(SessionService.CartCount)) return;
        MainThread.BeginInvokeOnMainThread(() =>
            CartTab.Title = _session.CartCount > 0 ? $"Giỏ ({_session.CartBadge})" : "Giỏ hàng");
    }
}

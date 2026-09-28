using Microsoft.Extensions.Logging;
using PaddyShop.Services;
using PaddyShop.ViewModels;
using PaddyShop.Views;

namespace PaddyShop;

public static class MauiProgram
{
    public static MauiApp CreateMauiApp()
    {
        var builder = MauiApp.CreateBuilder();
        builder.UseMauiApp<App>();

#if DEBUG
        builder.Logging.AddDebug();
#endif
        var s = builder.Services;

        // Dịch vụ dùng chung
        s.AddSingleton<SessionService>();
        s.AddSingleton<ShopApi>();
        s.AddSingleton<AppShell>();

        // Trang dạng tab: giữ nguyên trạng thái khi chuyển tab
        s.AddSingleton<HomeViewModel>();       s.AddSingleton<HomePage>();
        s.AddSingleton<ProductsViewModel>();   s.AddSingleton<ProductsPage>();
        s.AddSingleton<CartViewModel>();       s.AddSingleton<CartPage>();
        s.AddSingleton<OrdersViewModel>();     s.AddSingleton<OrdersPage>();
        s.AddSingleton<AccountViewModel>();    s.AddSingleton<AccountPage>();

        // Trang con: tạo mới mỗi lần mở
        s.AddTransient<ProductDetailViewModel>();  s.AddTransient<ProductDetailPage>();
        s.AddTransient<CheckoutViewModel>();       s.AddTransient<CheckoutPage>();
        s.AddTransient<OrderSuccessViewModel>();   s.AddTransient<OrderSuccessPage>();
        s.AddTransient<OrderDetailViewModel>();    s.AddTransient<OrderDetailPage>();
        s.AddTransient<NotificationsViewModel>();  s.AddTransient<NotificationsPage>();
        s.AddTransient<LoginViewModel>();          s.AddTransient<LoginPage>();
        s.AddTransient<RegisterViewModel>();       s.AddTransient<RegisterPage>();
        s.AddTransient<ForgotPasswordViewModel>(); s.AddTransient<ForgotPasswordPage>();
        s.AddTransient<EditProfileViewModel>();    s.AddTransient<EditProfilePage>();
        s.AddTransient<ChangePasswordViewModel>(); s.AddTransient<ChangePasswordPage>();
        s.AddTransient<ContactViewModel>();        s.AddTransient<ContactPage>();
        s.AddTransient<ServerSettingsViewModel>(); s.AddTransient<ServerSettingsPage>();

        return builder.Build();
    }
}

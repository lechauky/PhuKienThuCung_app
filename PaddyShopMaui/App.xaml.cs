namespace PaddyShop;

public partial class App : Application
{
    private readonly IServiceProvider _services;

    // Không nhận AppShell qua constructor: AppShell.xaml dùng {StaticResource Primary}...
    // nên phải tạo SAU khi InitializeComponent() đã nạp Colors.xaml / Styles.xaml.
    public App(IServiceProvider services)
    {
        InitializeComponent();
        UserAppTheme = AppTheme.Light; // giao diện sáng giống website
        _services = services;
    }

    protected override Window CreateWindow(IActivationState? activationState) =>
        new(_services.GetRequiredService<AppShell>()) { Title = "Paddy Pet Shop" };
}

using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class HomePage : ContentPage
{
    private readonly HomeViewModel _vm;
    private IDispatcherTimer? _bannerTimer;

    public HomePage(HomeViewModel vm)
    {
        InitializeComponent();
        BindingContext = _vm = vm;
        BannerCarousel.IndicatorView = BannerIndicator; // gán trong code để tránh lỗi tham chiếu tên trong XAML
    }

    protected override async void OnAppearing()
    {
        base.OnAppearing();
        StartBannerAutoScroll();
        await _vm.OnAppearingAsync();
    }

    protected override void OnDisappearing()
    {
        base.OnDisappearing();
        _bannerTimer?.Stop();
    }

    /// <summary>Tự chuyển banner sau mỗi 4 giây</summary>
    private void StartBannerAutoScroll()
    {
        _bannerTimer ??= CreateTimer();
        _bannerTimer.Start();
    }

    private IDispatcherTimer CreateTimer()
    {
        var t = Dispatcher.CreateTimer();
        t.Interval = TimeSpan.FromSeconds(4);
        t.Tick += (_, _) =>
        {
            var count = _vm.Banners.Count;
            if (count > 1) BannerCarousel.Position = (BannerCarousel.Position + 1) % count;
        };
        return t;
    }
}

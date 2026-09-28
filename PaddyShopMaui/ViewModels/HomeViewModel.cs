using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class HomeViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    private bool _loaded;

    public ObservableCollection<Banner> Banners { get; } = [];
    public ObservableCollection<Category> Categories { get; } = [];
    public ObservableCollection<Brand> TopBrands { get; } = [];
    public ObservableCollection<Product> OnSale { get; } = [];
    public ObservableCollection<Product> BestSellers { get; } = [];
    public ObservableCollection<Product> Newest { get; } = [];

    [ObservableProperty] private string searchText = "";
    [ObservableProperty] private bool isRefreshing;
    [ObservableProperty] private bool hasBanners;
    [ObservableProperty] private bool hasOnSale;

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (!_loaded) await LoadAsync();
        if (Session.IsLoggedIn)
        {
            // Cập nhật số giỏ hàng & thông báo chưa đọc (bỏ qua lỗi)
            try { await Api.NotificationsAsync(); } catch { }
            try { await Api.CartAsync(); } catch { }
        }
    }

    [RelayCommand]
    private async Task LoadAsync()
    {
        await RunAsync(async () =>
        {
            var d = await Api.HomeAsync();
            Reset(Banners, d.Banners);
            Reset(Categories, d.Categories);
            Reset(TopBrands, d.TopBrands);
            Reset(OnSale, d.OnSale);
            Reset(BestSellers, d.BestSellers);
            Reset(Newest, d.Newest);
            HasBanners = Banners.Count > 0;
            HasOnSale = OnSale.Count > 0;
            _loaded = true;
        }, showErrorBox: true);
        IsRefreshing = false;
    }

    private static void Reset<T>(ObservableCollection<T> target, IEnumerable<T> items)
    {
        target.Clear();
        foreach (var i in items) target.Add(i);
    }

    [RelayCommand]
    private Task Search()
    {
        var q = SearchText.Trim();
        return q.Length == 0 ? Task.CompletedTask : Routes.GoTabAsync(Routes.Products, "q=" + Uri.EscapeDataString(q));
    }

    [RelayCommand]
    private Task OpenCategory(Category? c) =>
        c == null ? Task.CompletedTask : Routes.GoTabAsync(Routes.Products, "category=" + Uri.EscapeDataString(c.Id));

    [RelayCommand]
    private Task OpenBrand(Brand? b) =>
        b == null ? Task.CompletedTask : Routes.GoTabAsync(Routes.Products, "brand=" + Uri.EscapeDataString(b.Id));

    [RelayCommand]
    private Task OpenBanner(Banner? b) =>
        string.IsNullOrEmpty(b?.ProductId) ? Task.CompletedTask : Routes.OpenProductAsync(b.ProductId);

    [RelayCommand]
    private Task SeeAll() => Routes.GoTabAsync(Routes.Products, "reset=1");

    [RelayCommand]
    private Task OpenCart() => Routes.GoTabAsync(Routes.Cart);

    [RelayCommand]
    private Task OpenNotifications() => Session.IsLoggedIn ? Routes.GoAsync(Routes.Notifications) : Routes.GoAsync(Routes.Login);
}

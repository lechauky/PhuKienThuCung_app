using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

/// <summary>Danh sách sản phẩm: tìm kiếm, lọc loại/thương hiệu/giá, sắp xếp, cuộn để tải thêm</summary>
public partial class ProductsViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session), IQueryAttributable
{
    private int _page;
    private int _totalPages;
    private bool _loaded;
    private bool _loadingMore;
    private HashSet<string> _pendingCategories = [];
    private HashSet<string> _pendingBrands = [];

    public ObservableCollection<Product> Products { get; } = [];
    public ObservableCollection<SelectableItem> CategoryOptions { get; } = [];
    public ObservableCollection<SelectableItem> BrandOptions { get; } = [];

    public List<string> SortOptions { get; } = ["Mặc định", "Mới nhất", "Giá tăng dần", "Giá giảm dần", "Tên A-Z"];
    private static readonly string?[] SortKeys = [null, "newest", "price_asc", "price_desc", "name"];

    [ObservableProperty] private string searchText = "";
    [ObservableProperty] private string minPriceText = "";
    [ObservableProperty] private string maxPriceText = "";
    [ObservableProperty] private int sortIndex;
    [ObservableProperty] private bool isFilterOpen;
    [ObservableProperty] private bool isRefreshing;
    [ObservableProperty] private bool isLoadingMore;
    [ObservableProperty] private string resultText = "";
    [ObservableProperty] private string filterButtonText = "Bộ lọc";
    [ObservableProperty] private bool isEmpty;

    /// <summary>Nhận tham số khi mở từ Trang chủ: ?q=..., ?category=..., ?brand=..., ?reset=1</summary>
    public void ApplyQueryAttributes(IDictionary<string, object> query)
    {
        if (query.Count == 0) return;
        string Get(string k) => query.TryGetValue(k, out var v) ? Uri.UnescapeDataString(v?.ToString() ?? "") : "";

        SearchText = Get("q");
        MinPriceText = MaxPriceText = "";
        SortIndex = 0;
        _pendingCategories = new HashSet<string>();
        _pendingBrands = new HashSet<string>();
        if (Get("category") is { Length: > 0 } cat) _pendingCategories.Add(cat);
        if (Get("brand") is { Length: > 0 } brand) _pendingBrands.Add(brand);
        foreach (var c in CategoryOptions) c.IsSelected = _pendingCategories.Contains(c.Id);
        foreach (var b in BrandOptions) b.IsSelected = _pendingBrands.Contains(b.Id);
        query.Clear();
        _loaded = false; // tải lại khi trang hiện ra
    }

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (CategoryOptions.Count == 0 || BrandOptions.Count == 0)
        {
            try
            {
                var cats = await Api.CategoriesAsync();
                var brands = await Api.BrandsAsync();
                CategoryOptions.Clear();
                foreach (var c in cats) CategoryOptions.Add(new SelectableItem { Id = c.Id, Name = c.Name, IsSelected = _pendingCategories.Contains(c.Id) });
                BrandOptions.Clear();
                foreach (var b in brands) BrandOptions.Add(new SelectableItem { Id = b.Id, Name = b.Name, IsSelected = _pendingBrands.Contains(b.Id) });
            }
            catch (ApiException) { /* bộ lọc sẽ trống, danh sách sản phẩm vẫn hiện */ }
        }
        if (!_loaded) await ReloadAsync();
    }

    private IEnumerable<string> SelectedCategories =>
        CategoryOptions.Count > 0 ? CategoryOptions.Where(c => c.IsSelected).Select(c => c.Id) : _pendingCategories;

    private IEnumerable<string> SelectedBrands =>
        BrandOptions.Count > 0 ? BrandOptions.Where(b => b.IsSelected).Select(b => b.Id) : _pendingBrands;

    private void UpdateFilterBadge()
    {
        var n = SelectedCategories.Count() + SelectedBrands.Count()
                + (ParsePrice(MinPriceText) > 0 || ParsePrice(MaxPriceText) > 0 ? 1 : 0) + (SortIndex > 0 ? 1 : 0);
        FilterButtonText = n > 0 ? $"Bộ lọc ({n})" : "Bộ lọc";
    }

    private static long? ParsePrice(string s) => long.TryParse(new string(s.Where(char.IsDigit).ToArray()), out var v) && v > 0 ? v : null;

    [RelayCommand]
    private async Task ReloadAsync()
    {
        _page = 0;
        _totalPages = 1;
        UpdateFilterBadge();
        var ok = await RunAsync(async () =>
        {
            var r = await Fetch(1);
            Products.Clear();
            foreach (var p in r.Items) Products.Add(p);
        }, showErrorBox: true);
        _loaded = ok;
        IsRefreshing = false;
    }

    private async Task<Paged<Product>> Fetch(int page)
    {
        var r = await Api.ProductsAsync(SearchText.Trim(), SelectedCategories, SelectedBrands,
            ParsePrice(MinPriceText), ParsePrice(MaxPriceText), SortKeys[Math.Clamp(SortIndex, 0, SortKeys.Length - 1)], page);
        _page = r.Page;
        _totalPages = r.TotalPages;
        ResultText = $"{r.Total} sản phẩm" + (string.IsNullOrWhiteSpace(SearchText) ? "" : $" cho \"{SearchText.Trim()}\"");
        IsEmpty = r.Total == 0;
        return r;
    }

    [RelayCommand]
    private async Task LoadMoreAsync()
    {
        if (_loadingMore || IsBusy || _page >= _totalPages) return;
        _loadingMore = true;
        IsLoadingMore = true;
        await RunAsync(async () =>
        {
            var r = await Fetch(_page + 1);
            foreach (var p in r.Items) Products.Add(p);
        }, useBusy: false);
        IsLoadingMore = false;
        _loadingMore = false;
    }

    [RelayCommand]
    private void ToggleFilter() => IsFilterOpen = !IsFilterOpen;

    [RelayCommand]
    private void ToggleOption(SelectableItem? item)
    {
        if (item != null) item.IsSelected = !item.IsSelected;
    }

    [RelayCommand]
    private async Task ApplyFilterAsync()
    {
        IsFilterOpen = false;
        await ReloadAsync();
    }

    [RelayCommand]
    private async Task ClearFilterAsync()
    {
        foreach (var c in CategoryOptions) c.IsSelected = false;
        foreach (var b in BrandOptions) b.IsSelected = false;
        _pendingCategories = [];
        _pendingBrands = [];
        MinPriceText = MaxPriceText = "";
        SortIndex = 0;
        IsFilterOpen = false;
        await ReloadAsync();
    }

    [RelayCommand]
    private async Task RefreshAsync()
    {
        IsRefreshing = true;
        await ReloadAsync();
    }
}

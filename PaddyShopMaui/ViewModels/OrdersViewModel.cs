using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class OrdersViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    private int _page;
    private int _totalPages;
    private bool _loadingMore;

    public ObservableCollection<Order> Orders { get; } = [];

    /// <summary>Id = tình trạng đơn ("" = tất cả): 3 chưa xác nhận, 2 đang giao, 1 đã giao, 0 đã hủy</summary>
    public ObservableCollection<SelectableItem> StatusFilters { get; } =
    [
        new() { Id = "", Name = "Tất cả", IsSelected = true },
        new() { Id = "3", Name = "Chưa xác nhận" },
        new() { Id = "2", Name = "Đang giao" },
        new() { Id = "1", Name = "Đã giao" },
        new() { Id = "0", Name = "Đã hủy" },
    ];

    [ObservableProperty] private bool isRefreshing;
    [ObservableProperty] private bool isEmpty;

    private int? CurrentStatus
    {
        get
        {
            var id = StatusFilters.FirstOrDefault(s => s.IsSelected)?.Id;
            return int.TryParse(id, out var s) ? s : null;
        }
    }

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Session.IsLoggedIn) await ReloadAsync();
        else { Orders.Clear(); IsEmpty = false; }
    }

    [RelayCommand]
    private async Task ReloadAsync()
    {
        await RunAsync(async () =>
        {
            var r = await Api.OrdersAsync(1, CurrentStatus);
            _page = r.Page;
            _totalPages = r.TotalPages;
            Orders.Clear();
            foreach (var o in r.Items) Orders.Add(o);
            IsEmpty = Orders.Count == 0;
        }, showErrorBox: true);
        IsRefreshing = false;
    }

    [RelayCommand]
    private async Task LoadMoreAsync()
    {
        if (_loadingMore || IsBusy || _page >= _totalPages) return;
        _loadingMore = true;
        await RunAsync(async () =>
        {
            var r = await Api.OrdersAsync(_page + 1, CurrentStatus);
            _page = r.Page;
            _totalPages = r.TotalPages;
            foreach (var o in r.Items) Orders.Add(o);
        }, useBusy: false);
        _loadingMore = false;
    }

    [RelayCommand]
    private async Task SelectStatusAsync(SelectableItem? item)
    {
        if (item == null || item.IsSelected) return;
        foreach (var s in StatusFilters) s.IsSelected = s == item;
        await ReloadAsync();
    }

    [RelayCommand]
    private Task OpenOrder(Order? o) => o == null ? Task.CompletedTask : Routes.OpenOrderAsync(o.Id);
}

public partial class OrderDetailViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session), IQueryAttributable
{
    private string _id = "";

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasOrder))]
    private Order? order;

    public bool HasOrder => Order != null;

    public void ApplyQueryAttributes(IDictionary<string, object> query)
    {
        if (query.TryGetValue("id", out var v)) _id = Uri.UnescapeDataString(v?.ToString() ?? "");
    }

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        await LoadAsync();
    }

    [RelayCommand]
    private async Task LoadAsync() => await RunAsync(async () => Order = await Api.OrderAsync(_id), showErrorBox: true);

    /// <summary>Khách tự hủy đơn khi cửa hàng chưa xác nhận</summary>
    [RelayCommand]
    private async Task CancelOrderAsync()
    {
        if (Order is not { CanCancel: true } current) return;
        var id = current.Id;
        var reason = await Notifier.PromptAsync("Hủy đơn hàng", $"Bạn muốn hủy đơn {id}?\nLý do (không bắt buộc):", "Vd: Đặt nhầm sản phẩm");
        if (reason == null) return; // bấm Hủy trên hộp thoại
        await RunAsync(async () =>
        {
            Order = await Api.CancelOrderAsync(id, reason);
            Notifier.Toast("Đã hủy đơn hàng");
        });
    }

    [RelayCommand]
    private Task OpenItem(OrderItem? item) => item == null ? Task.CompletedTask : Routes.OpenProductAsync(item.ProductId);
}

public partial class NotificationsViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    public ObservableCollection<NotificationItem> Items { get; } = [];

    [ObservableProperty] private bool isRefreshing;
    [ObservableProperty] private bool isEmpty;

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        await LoadAsync();
    }

    [RelayCommand]
    private async Task LoadAsync()
    {
        await RunAsync(async () =>
        {
            var n = await Api.NotificationsAsync();
            Items.Clear();
            foreach (var i in n.Items) Items.Add(i);
            IsEmpty = Items.Count == 0;
        }, showErrorBox: true);
        IsRefreshing = false;
    }

    [RelayCommand]
    private async Task OpenAsync(NotificationItem? item)
    {
        if (item == null) return;
        if (!item.IsRead)
        {
            try { await Api.MarkNotificationReadAsync(item.Id); } catch (ApiException) { }
            await LoadAsync();
        }
        if (!string.IsNullOrEmpty(item.OrderId)) await Routes.OpenOrderAsync(item.OrderId);
        else await Notifier.AlertAsync(item.Title, item.Content);
    }

    [RelayCommand]
    private async Task MarkAllReadAsync()
    {
        await RunAsync(() => Api.MarkNotificationReadAsync(null));
        await LoadAsync();
    }
}

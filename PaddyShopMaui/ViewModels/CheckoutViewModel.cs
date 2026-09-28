using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class CheckoutViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    public ObservableCollection<CartItem> Items { get; } = [];

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasItems))]
    private Cart? cart;

    [ObservableProperty] private string note = "";
    [ObservableProperty] private bool isPlacing;

    public bool HasItems => Cart is { Items.Count: > 0 };

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        await LoadAsync(); // tải lại cả khi quay về từ trang Sửa thông tin
    }

    [RelayCommand]
    private async Task LoadAsync()
    {
        await RunAsync(async () =>
        {
            var c = await Api.CartAsync();
            Cart = c;
            Items.Clear();
            foreach (var i in c.Items) Items.Add(i);
        }, showErrorBox: true);
    }

    [RelayCommand]
    private Task EditProfile() => Routes.GoAsync(Routes.EditProfile);

    [RelayCommand]
    private async Task PlaceOrderAsync()
    {
        if (IsPlacing || !HasItems) return;
        IsPlacing = true;
        try
        {
            var order = await Api.CheckoutAsync(Note);
            Note = "";
            // Bỏ trang đặt hàng khỏi ngăn xếp rồi mở trang thành công
            await Shell.Current.GoToAsync($"../{Routes.OrderSuccess}?id={Uri.EscapeDataString(order.Id)}");
        }
        catch (ApiException e)
        {
            await Notifier.AlertAsync("Không đặt được hàng", e.Message);
            await LoadAsync(); // giỏ có thể đã được máy chủ điều chỉnh theo tồn kho
        }
        finally
        {
            IsPlacing = false;
        }
    }
}

public partial class OrderSuccessViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session), IQueryAttributable
{
    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(Message))]
    private string orderId = "";

    public string Message => $"Mã đơn hàng của bạn là {OrderId}.\nCửa hàng sẽ sớm xác nhận và giao hàng cho bạn.";

    public void ApplyQueryAttributes(IDictionary<string, object> query)
    {
        if (query.TryGetValue("id", out var v)) OrderId = Uri.UnescapeDataString(v?.ToString() ?? "");
    }

    [RelayCommand]
    private Task ViewOrder() => Shell.Current.GoToAsync($"../{Routes.OrderDetail}?id={Uri.EscapeDataString(OrderId)}");

    [RelayCommand]
    private Task ContinueShopping() => Routes.GoTabAsync(Routes.Home);
}

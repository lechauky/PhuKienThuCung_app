using System.Collections.ObjectModel;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class CartViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session)
{
    public ObservableCollection<CartItem> Items { get; } = [];

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasItems), nameof(IsEmpty))]
    private Cart? cart;

    [ObservableProperty] private bool isRefreshing;

    public bool HasItems => Cart is { Items.Count: > 0 };
    public bool IsEmpty => Cart != null && Cart.Items.Count == 0;

    /// <summary>Mỗi lần mở tab giỏ thì tải lại (máy chủ đồng bộ số lượng theo tồn kho, giống update_cart_first.php)</summary>
    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Session.IsLoggedIn) await LoadAsync();
        else { Cart = null; Items.Clear(); }
    }

    private void Show(Cart c)
    {
        Cart = c;
        Items.Clear();
        foreach (var i in c.Items) Items.Add(i);
    }

    [RelayCommand]
    private async Task LoadAsync()
    {
        await RunAsync(async () => Show(await Api.CartAsync()), showErrorBox: true);
        IsRefreshing = false;
    }

    [RelayCommand]
    private async Task IncreaseAsync(CartItem? item)
    {
        if (item == null) return;
        if (!item.CanIncrease)
        {
            Notifier.Toast($"Số lượng tối đa cho sản phẩm này là {item.Product.Stock}");
            return;
        }
        await RunAsync(async () => Show(await Api.UpdateCartAsync(item.Product.Id, item.Quantity + 1)));
    }

    [RelayCommand]
    private async Task DecreaseAsync(CartItem? item)
    {
        if (item == null) return;
        if (item.Quantity <= 1)
        {
            await RemoveAsync(item); // giống web: còn 1 mà bấm giảm thì hỏi xóa
            return;
        }
        await RunAsync(async () => Show(await Api.UpdateCartAsync(item.Product.Id, item.Quantity - 1)));
    }

    [RelayCommand]
    private async Task RemoveAsync(CartItem? item)
    {
        if (item == null) return;
        if (!await Notifier.ConfirmAsync("Xóa sản phẩm", $"Bạn có chắc muốn xóa \"{item.Product.Name}\" khỏi giỏ hàng?", "Xóa")) return;
        await RunAsync(async () => Show(await Api.RemoveFromCartAsync(item.Product.Id)));
    }

    [RelayCommand]
    private Task Checkout() => HasItems ? Routes.GoAsync(Routes.Checkout) : Task.CompletedTask;

    [RelayCommand]
    private Task Shop() => Routes.GoTabAsync(Routes.Products);

    [RelayCommand]
    private Task OpenItem(CartItem? item) => item == null ? Task.CompletedTask : Routes.OpenProductAsync(item.Product.Id);
}

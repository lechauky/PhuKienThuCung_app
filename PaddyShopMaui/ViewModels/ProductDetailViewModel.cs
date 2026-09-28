using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

public partial class ProductDetailViewModel(ShopApi api, SessionService session) : BaseViewModel(api, session), IQueryAttributable
{
    private string _id = "";

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(HasProduct), nameof(HasRelated))]
    private Product? product;

    [ObservableProperty]
    [NotifyCanExecuteChangedFor(nameof(IncreaseCommand), nameof(DecreaseCommand))]
    private int quantity = 1;

    public bool HasProduct => Product != null;
    public bool HasRelated => Product?.Related is { Count: > 0 };

    public void ApplyQueryAttributes(IDictionary<string, object> query)
    {
        if (query.TryGetValue("id", out var v)) _id = Uri.UnescapeDataString(v?.ToString() ?? "");
    }

    public override async Task OnAppearingAsync()
    {
        await base.OnAppearingAsync();
        if (Product == null || Product.Id != _id) await LoadAsync();
    }

    [RelayCommand]
    private async Task LoadAsync()
    {
        await RunAsync(async () =>
        {
            Product = await Api.ProductAsync(_id);
            Quantity = 1;
            IncreaseCommand.NotifyCanExecuteChanged();
            DecreaseCommand.NotifyCanExecuteChanged();
        }, showErrorBox: true);
    }

    private bool CanIncrease() => Product != null && Quantity < Product.Stock;
    private bool CanDecrease() => Quantity > 1;

    [RelayCommand(CanExecute = nameof(CanIncrease))]
    private void Increase() => Quantity++;

    [RelayCommand(CanExecute = nameof(CanDecrease))]
    private void Decrease() => Quantity--;

    [RelayCommand]
    private Task AddCurrent() => AddToCartCoreAsync(Product, Quantity);

    /// <summary>Mua ngay: thêm vào giỏ xong mới mở giỏ hàng</summary>
    [RelayCommand]
    private async Task BuyNowAsync()
    {
        if (Product == null) return;
        if (!Session.IsLoggedIn)
        {
            await AddToCartCoreAsync(Product, Quantity); // chuyển sang đăng nhập
            return;
        }
        try
        {
            await Api.AddToCartAsync(Product.Id, Quantity);
            await Routes.GoTabAsync(Routes.Cart);
        }
        catch (ApiException e)
        {
            Notifier.Toast(e.Message);
            if (e.Status == 409) await Routes.GoTabAsync(Routes.Cart); // đã đủ số lượng tối đa trong giỏ
        }
    }

    [RelayCommand]
    private Task OpenCart() => Routes.GoTabAsync(Routes.Cart);
}

using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class CartPage : ContentPage
{
    private readonly CartViewModel _vm;

    public CartPage(CartViewModel vm)
    {
        InitializeComponent();
        BindingContext = _vm = vm;
    }

    protected override async void OnAppearing()
    {
        base.OnAppearing();
        await _vm.OnAppearingAsync();
    }
}

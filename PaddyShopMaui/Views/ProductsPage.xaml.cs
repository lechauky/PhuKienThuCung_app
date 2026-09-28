using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class ProductsPage : ContentPage
{
    private readonly ProductsViewModel _vm;

    public ProductsPage(ProductsViewModel vm)
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

using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class ProductDetailPage : ContentPage
{
    private readonly ProductDetailViewModel _vm;

    public ProductDetailPage(ProductDetailViewModel vm)
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

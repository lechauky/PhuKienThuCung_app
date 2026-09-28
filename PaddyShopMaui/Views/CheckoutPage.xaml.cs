using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class CheckoutPage : ContentPage
{
    private readonly CheckoutViewModel _vm;

    public CheckoutPage(CheckoutViewModel vm)
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

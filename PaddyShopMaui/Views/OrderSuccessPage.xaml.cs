using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class OrderSuccessPage : ContentPage
{
    private readonly OrderSuccessViewModel _vm;

    public OrderSuccessPage(OrderSuccessViewModel vm)
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

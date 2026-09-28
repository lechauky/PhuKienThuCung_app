using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class OrderDetailPage : ContentPage
{
    private readonly OrderDetailViewModel _vm;

    public OrderDetailPage(OrderDetailViewModel vm)
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

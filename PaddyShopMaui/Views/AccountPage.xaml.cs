using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class AccountPage : ContentPage
{
    private readonly AccountViewModel _vm;

    public AccountPage(AccountViewModel vm)
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

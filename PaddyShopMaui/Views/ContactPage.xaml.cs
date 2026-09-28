using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class ContactPage : ContentPage
{
    private readonly ContactViewModel _vm;

    public ContactPage(ContactViewModel vm)
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

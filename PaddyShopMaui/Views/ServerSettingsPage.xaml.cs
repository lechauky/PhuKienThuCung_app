using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class ServerSettingsPage : ContentPage
{
    private readonly ServerSettingsViewModel _vm;

    public ServerSettingsPage(ServerSettingsViewModel vm)
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

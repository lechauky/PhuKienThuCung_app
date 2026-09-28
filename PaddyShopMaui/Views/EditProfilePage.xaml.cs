using PaddyShop.ViewModels;

namespace PaddyShop.Views;

public partial class EditProfilePage : ContentPage
{
    private readonly EditProfileViewModel _vm;

    public EditProfilePage(EditProfileViewModel vm)
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

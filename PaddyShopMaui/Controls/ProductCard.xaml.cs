using System.Windows.Input;

namespace PaddyShop.Controls;

/// <summary>
/// Thẻ sản phẩm. Trang chứa truyền 2 lệnh: TapCommand (mở chi tiết) và AddCommand (thêm vào giỏ);
/// tham số của lệnh là sản phẩm (BindingContext của thẻ).
/// </summary>
public partial class ProductCard : ContentView
{
    public static readonly BindableProperty TapCommandProperty =
        BindableProperty.Create(nameof(TapCommand), typeof(ICommand), typeof(ProductCard));

    public static readonly BindableProperty AddCommandProperty =
        BindableProperty.Create(nameof(AddCommand), typeof(ICommand), typeof(ProductCard));

    public ICommand? TapCommand
    {
        get => (ICommand?)GetValue(TapCommandProperty);
        set => SetValue(TapCommandProperty, value);
    }

    public ICommand? AddCommand
    {
        get => (ICommand?)GetValue(AddCommandProperty);
        set => SetValue(AddCommandProperty, value);
    }

    public ProductCard()
    {
        InitializeComponent();
    }

    private void OnCardTapped(object? sender, TappedEventArgs e)
    {
        if (TapCommand?.CanExecute(BindingContext) == true) TapCommand.Execute(BindingContext);
    }

    private void OnAddClicked(object? sender, EventArgs e)
    {
        if (AddCommand?.CanExecute(BindingContext) == true) AddCommand.Execute(BindingContext);
    }
}

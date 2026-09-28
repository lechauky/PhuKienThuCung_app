namespace PaddyShop.Services;

/// <summary>Tên các route điều hướng trong AppShell</summary>
public static class Routes
{
    // Tab (điều hướng tuyệt đối bằng "//")
    public const string Home = "home";
    public const string Products = "products";
    public const string Cart = "cart";
    public const string Orders = "orders";
    public const string Account = "account";

    // Trang con (đẩy lên ngăn xếp)
    public const string ProductDetail = "product";
    public const string Checkout = "checkout";
    public const string OrderSuccess = "ordersuccess";
    public const string OrderDetail = "orderdetail";
    public const string Notifications = "notifications";
    public const string Login = "login";
    public const string Register = "register";
    public const string ForgotPassword = "forgotpassword";
    public const string EditProfile = "editprofile";
    public const string ChangePassword = "changepassword";
    public const string Contact = "contact";
    public const string Server = "server";

    public static Task GoAsync(string route) => Shell.Current.GoToAsync(route);

    public static Task GoTabAsync(string tab, string? query = null) =>
        Shell.Current.GoToAsync("//" + tab + (string.IsNullOrEmpty(query) ? "" : "?" + query));

    public static Task OpenProductAsync(string id) => Shell.Current.GoToAsync($"{ProductDetail}?id={Uri.EscapeDataString(id)}");

    public static Task OpenOrderAsync(string id) => Shell.Current.GoToAsync($"{OrderDetail}?id={Uri.EscapeDataString(id)}");

    public static Task BackAsync() => Shell.Current.GoToAsync("..");
}

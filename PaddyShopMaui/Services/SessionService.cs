using System.Text.Json;
using CommunityToolkit.Mvvm.ComponentModel;
using PaddyShop.Models;

namespace PaddyShop.Services;

/// <summary>
/// Trạng thái dùng chung toàn app: khách đang đăng nhập, token, địa chỉ máy chủ,
/// số loại sản phẩm trong giỏ và số thông báo chưa đọc.
/// Token lưu bằng SecureStorage (mã hóa), thông tin khác lưu bằng Preferences.
/// </summary>
public partial class SessionService : ObservableObject
{
    /// <summary>Địa chỉ mặc định: 10.0.2.2 là "localhost" của máy tính khi chạy Android Emulator</summary>
    public const string DefaultBaseUrl = "http://10.0.2.2/WebPhuKienThuCung/";

    private const string KeyToken = "paddy_token";
    private const string KeyCustomer = "paddy_customer";
    private const string KeyBaseUrl = "paddy_base_url";

    private Task? _loadTask;

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(IsLoggedIn), nameof(IsGuest))]
    private Customer? customer;

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(CartBadge), nameof(HasCartItems))]
    private int cartCount;

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(UnreadBadge), nameof(HasUnread))]
    private int unreadCount;

    [ObservableProperty]
    private string baseUrl = DefaultBaseUrl;

    public string? Token { get; private set; }

    public bool IsLoggedIn => Customer != null && !string.IsNullOrEmpty(Token);
    public bool IsGuest => !IsLoggedIn;
    public string CartBadge => CartCount > 99 ? "99+" : CartCount.ToString();
    public bool HasCartItems => CartCount > 0;
    public string UnreadBadge => UnreadCount > 99 ? "99+" : UnreadCount.ToString();
    public bool HasUnread => UnreadCount > 0;

    /// <summary>Đọc phiên đăng nhập đã lưu (chỉ chạy 1 lần, các lần gọi sau dùng lại kết quả)</summary>
    public Task EnsureLoadedAsync() => _loadTask ??= LoadAsync();

    private async Task LoadAsync()
    {
        BaseUrl = Preferences.Default.Get(KeyBaseUrl, DefaultBaseUrl);
        try
        {
            Token = await SecureStorage.Default.GetAsync(KeyToken);
        }
        catch
        {
            Token = null; // SecureStorage lỗi (vd. đổi khóa máy) -> coi như chưa đăng nhập
        }
        var json = Preferences.Default.Get<string?>(KeyCustomer, null);
        Customer? c = null;
        if (!string.IsNullOrEmpty(json) && Token != null)
        {
            try { c = JsonSerializer.Deserialize(json, AppJsonContext.Default.Customer); } catch { }
        }
        Customer = c;
        OnPropertyChanged(nameof(IsLoggedIn));
        OnPropertyChanged(nameof(IsGuest));
    }

    public async Task SaveLoginAsync(string token, Customer customer)
    {
        Token = token;
        await SecureStorage.Default.SetAsync(KeyToken, token);
        SaveCustomer(customer);
    }

    public async Task SaveTokenAsync(string token)
    {
        Token = token;
        await SecureStorage.Default.SetAsync(KeyToken, token);
    }

    public void SaveCustomer(Customer customer)
    {
        Preferences.Default.Set(KeyCustomer, JsonSerializer.Serialize(customer, AppJsonContext.Default.Customer));
        Customer = customer;
        OnPropertyChanged(nameof(IsLoggedIn));
        OnPropertyChanged(nameof(IsGuest));
    }

    public void Logout()
    {
        Token = null;
        SecureStorage.Default.Remove(KeyToken);
        Preferences.Default.Remove(KeyCustomer);
        Customer = null;
        CartCount = 0;
        UnreadCount = 0;
        OnPropertyChanged(nameof(IsLoggedIn));
        OnPropertyChanged(nameof(IsGuest));
    }

    public void SaveBaseUrl(string url)
    {
        BaseUrl = NormalizeUrl(url);
        Preferences.Default.Set(KeyBaseUrl, BaseUrl);
    }

    /// <summary>Thêm http:// nếu thiếu và luôn kết thúc bằng "/"</summary>
    public static string NormalizeUrl(string input)
    {
        var url = input.Trim();
        if (!url.StartsWith("http://", StringComparison.OrdinalIgnoreCase) &&
            !url.StartsWith("https://", StringComparison.OrdinalIgnoreCase))
            url = "http://" + url;
        if (!url.EndsWith('/')) url += "/";
        return url;
    }
}

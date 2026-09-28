using System.Net;
using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using System.Text.Json.Serialization.Metadata;
using PaddyShop.Models;

namespace PaddyShop.Services;

/// <summary>Lỗi hiển thị được cho người dùng (message tiếng Việt từ máy chủ hoặc từ app)</summary>
public class ApiException(string message, int status = 0) : Exception(message)
{
    public int Status { get; } = status;
}

/// <summary>
/// Gọi REST API của website (thư mục api/, dạng api/index.php?r=&lt;route&gt;).
/// Mọi phản hồi có dạng { success, message, data }. Route cần đăng nhập gửi kèm "Authorization: Bearer &lt;token&gt;".
/// </summary>
public class ShopApi
{
    private readonly SessionService _session;
    private readonly HttpClient _http;
    private static readonly AppJsonContext Json = AppJsonContext.Default;

    public ShopApi(SessionService session)
    {
        _session = session;
        // Chờ lâu hơn bình thường vì chức năng quên mật khẩu phải gửi email
        _http = new HttpClient { Timeout = TimeSpan.FromSeconds(40) };
    }

    /* =============================== Gọi API chung =============================== */

    private string BuildUrl(string route, IEnumerable<KeyValuePair<string, string?>>? query)
    {
        var sb = new StringBuilder(_session.BaseUrl).Append("api/index.php?r=").Append(route);
        if (query != null)
        {
            foreach (var (k, v) in query)
            {
                if (string.IsNullOrEmpty(v)) continue;
                sb.Append('&').Append(k).Append('=').Append(Uri.EscapeDataString(v));
            }
        }
        return sb.ToString();
    }

    private async Task<ApiResponse<T>> SendAsync<T>(HttpMethod method, string route,
        JsonTypeInfo<ApiResponse<T>> resultInfo,
        IEnumerable<KeyValuePair<string, string?>>? query = null,
        HttpContent? content = null)
    {
        await _session.EnsureLoadedAsync();
        var url = BuildUrl(route, query);
        using var request = new HttpRequestMessage(method, url) { Content = content };
        if (!string.IsNullOrEmpty(_session.Token))
            request.Headers.Authorization = new AuthenticationHeaderValue("Bearer", _session.Token);

        HttpResponseMessage response;
        string raw;
        try
        {
            response = await _http.SendAsync(request);
            raw = await response.Content.ReadAsStringAsync();
        }
        catch (Exception e) when (e is HttpRequestException or TaskCanceledException or IOException)
        {
            throw new ApiException($"Không kết nối được máy chủ {_session.BaseUrl}\nHãy kiểm tra XAMPP đang chạy và địa chỉ máy chủ (Tài khoản > Cấu hình máy chủ).");
        }

        ApiResponse<T>? body;
        try
        {
            body = JsonSerializer.Deserialize(raw, resultInfo);
        }
        catch (JsonException)
        {
            body = null;
        }

        var code = (int)response.StatusCode;
        if (response.StatusCode == HttpStatusCode.Unauthorized && _session.Token != null)
        {
            _session.Logout();
            throw new ApiException(body?.Message ?? "Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại", 401);
        }
        if (body == null)
            throw new ApiException(code == 200 || code == 404
                ? "Máy chủ trả về dữ liệu không hợp lệ. Kiểm tra lại địa chỉ máy chủ (phải trỏ tới thư mục website có chứa api/)."
                : $"Lỗi máy chủ (HTTP {code})", code);
        if (!response.IsSuccessStatusCode || !body.Success)
            throw new ApiException(body.Message ?? $"Có lỗi xảy ra (HTTP {code})", code);
        return body;
    }

    private Task<ApiResponse<T>> GetAsync<T>(string route, JsonTypeInfo<ApiResponse<T>> info, params (string Key, string? Value)[] query) =>
        SendAsync(HttpMethod.Get, route, info, query.Select(q => new KeyValuePair<string, string?>(q.Key, q.Value)));

    private Task<ApiResponse<T>> PostAsync<TBody, T>(string route, TBody body, JsonTypeInfo<TBody> bodyInfo, JsonTypeInfo<ApiResponse<T>> info)
    {
        var json = JsonSerializer.Serialize(body, bodyInfo);
        return SendAsync(HttpMethod.Post, route, info, null, new StringContent(json, Encoding.UTF8, "application/json"));
    }

    private static T Require<T>(ApiResponse<T> r) => r.Data ?? throw new ApiException("Máy chủ không trả về dữ liệu");

    /* ================================ Sản phẩm ================================ */

    public async Task<HomeData> HomeAsync() => Require(await GetAsync("home", Json.ApiResponseHomeData));

    public async Task<List<Category>> CategoriesAsync() => Require(await GetAsync("categories", Json.ApiResponseListCategory));

    public async Task<List<Brand>> BrandsAsync() => Require(await GetAsync("brands", Json.ApiResponseListBrand));

    public async Task<Paged<Product>> ProductsAsync(string? q, IEnumerable<string> categories, IEnumerable<string> brands,
        long? minPrice, long? maxPrice, string? sort, int page, int pageSize = 10)
    {
        var r = await GetAsync("products", Json.ApiResponsePagedProduct,
            ("q", q),
            ("categories", string.Join(",", categories)),
            ("brands", string.Join(",", brands)),
            ("minPrice", minPrice > 0 ? minPrice.ToString() : null),
            ("maxPrice", maxPrice > 0 ? maxPrice.ToString() : null),
            ("sort", sort),
            ("page", page.ToString()),
            ("pageSize", pageSize.ToString()));
        return Require(r);
    }

    public async Task<Product> ProductAsync(string id) => Require(await GetAsync("product", Json.ApiResponseProduct, ("id", id)));

    /* ================================ Địa chỉ ================================ */

    public async Task<List<Place>> ProvincesAsync() => Require(await GetAsync("provinces", Json.ApiResponseListPlace));
    public async Task<List<Place>> DistrictsAsync(string provinceId) => Require(await GetAsync("districts", Json.ApiResponseListPlace, ("provinceId", provinceId)));
    public async Task<List<Place>> WardsAsync(string districtId) => Require(await GetAsync("wards", Json.ApiResponseListPlace, ("districtId", districtId)));
    public async Task<ShopInfo> ShopInfoAsync() => Require(await GetAsync("shop_info", Json.ApiResponseShopInfo));

    /* ================================ Tài khoản ================================ */

    public async Task<bool> IsUsernameAvailableAsync(string username) =>
        Require(await GetAsync("check_username", Json.ApiResponseUsernameCheck, ("username", username))).Available;

    private async Task OnAuthenticatedAsync(AuthResult r)
    {
        await _session.SaveLoginAsync(r.Token, r.Customer);
        try { await CartAsync(); } catch (ApiException) { }
        try { await NotificationsAsync(); } catch (ApiException) { }
    }

    public async Task LoginAsync(string username, string password) =>
        await OnAuthenticatedAsync(Require(await PostAsync("login", new LoginRequest(username.Trim(), password), Json.LoginRequest, Json.ApiResponseAuthResult)));

    public async Task RegisterAsync(ProfileRequest req) =>
        await OnAuthenticatedAsync(Require(await PostAsync("register", req, Json.ProfileRequest, Json.ApiResponseAuthResult)));

    public async Task<Customer> RefreshMeAsync()
    {
        var c = Require(await GetAsync("me", Json.ApiResponseCustomer));
        _session.SaveCustomer(c);
        return c;
    }

    public async Task<Customer> UpdateProfileAsync(ProfileRequest req)
    {
        var c = Require(await PostAsync("update_profile", req, Json.ProfileRequest, Json.ApiResponseCustomer));
        _session.SaveCustomer(c);
        return c;
    }

    public async Task<string> ChangePasswordAsync(string oldPassword, string newPassword)
    {
        var r = await PostAsync("change_password", new ChangePasswordRequest(oldPassword, newPassword), Json.ChangePasswordRequest, Json.ApiResponseTokenOnly);
        if (r.Data?.Token is { Length: > 0 } t) await _session.SaveTokenAsync(t);  // token cũ hết hạn khi đổi mật khẩu
        return r.Message ?? "Đổi mật khẩu thành công";
    }

    public async Task<string> ForgotPasswordAsync(string email) =>
        (await PostAsync("forgot_password", new EmailRequest(email.Trim()), Json.EmailRequest, Json.ApiResponseJsonElement)).Message ?? "Đã gửi mã xác nhận";

    public async Task<string> ResetPasswordAsync(string email, string code, string newPassword) =>
        (await PostAsync("reset_password", new ResetPasswordRequest(email.Trim(), code.Trim(), newPassword), Json.ResetPasswordRequest, Json.ApiResponseJsonElement)).Message
        ?? "Đặt lại mật khẩu thành công";

    public void Logout() => _session.Logout();

    /* ================================ Giỏ hàng ================================ */

    private Cart Track(Cart c)
    {
        _session.CartCount = c.ItemCount;
        return c;
    }

    public async Task<Cart> CartAsync() => Track(Require(await GetAsync("cart", Json.ApiResponseCart)));

    /// <summary>Thêm vào giỏ, trả về thông báo của máy chủ ("Đã thêm vào giỏ" ...)</summary>
    public async Task<string> AddToCartAsync(string productId, int quantity = 1)
    {
        var r = await PostAsync("cart_add", new CartRequest(productId, quantity), Json.CartRequest, Json.ApiResponseCart);
        Track(Require(r));
        return r.Message ?? "Đã thêm vào giỏ";
    }

    public async Task<Cart> UpdateCartAsync(string productId, int quantity) =>
        Track(Require(await PostAsync("cart_update", new CartRequest(productId, quantity), Json.CartRequest, Json.ApiResponseCart)));

    public async Task<Cart> RemoveFromCartAsync(string productId) =>
        Track(Require(await PostAsync("cart_remove", new CartRequest(productId), Json.CartRequest, Json.ApiResponseCart)));

    /* ================================ Đơn hàng ================================ */

    public async Task<Order> CheckoutAsync(string? note)
    {
        var o = Require(await PostAsync("checkout", new CheckoutRequest(string.IsNullOrWhiteSpace(note) ? null : note.Trim()), Json.CheckoutRequest, Json.ApiResponseOrder));
        _session.CartCount = 0;
        return o;
    }

    public async Task<Paged<Order>> OrdersAsync(int page, int? status) =>
        Require(await GetAsync("orders", Json.ApiResponsePagedOrder, ("page", page.ToString()), ("status", status?.ToString())));

    public async Task<Order> OrderAsync(string id) => Require(await GetAsync("order", Json.ApiResponseOrder, ("id", id)));

    public async Task<Order> CancelOrderAsync(string id, string? reason) =>
        Require(await PostAsync("cancel_order", new CancelOrderRequest(id, reason), Json.CancelOrderRequest, Json.ApiResponseOrder));

    /* ================================ Thông báo ================================ */

    public async Task<NotificationList> NotificationsAsync()
    {
        var n = Require(await GetAsync("notifications", Json.ApiResponseNotificationList));
        _session.UnreadCount = n.Unread;
        return n;
    }

    /// <summary>id = null: đánh dấu tất cả đã đọc</summary>
    public async Task MarkNotificationReadAsync(int? id) =>
        await PostAsync("notifications_read", new ReadNotificationRequest(id), Json.ReadNotificationRequest, Json.ApiResponseJsonElement);
}

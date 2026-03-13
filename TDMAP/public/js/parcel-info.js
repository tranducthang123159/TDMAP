function showParcelInfo(p) {

    let html = `
<div class="parcel-box">

<h5>Thông tin thửa đất</h5>

<table class="table table-sm">

<tr>
<td><b>Tờ bản đồ</b></td>
<td>${p.SHBANDO ?? ""}</td>
</tr>

<tr>
<td><b>Số thửa</b></td>
<td>${p.SHTHUA ?? ""}</td>
</tr>

<tr>
<td><b>Diện tích</b></td>
<td>${p.DIENTICH ?? ""} m²</td>
</tr>

<tr>
<td><b>Loại đất</b></td>
<td>${p.KHLOAIDAT ?? ""}</td>
</tr>

</table>

<hr>

<h6>Thông tin người sử dụng</h6>

<table class="table table-sm">

<tr>
<td><b>Chủ sử dụng</b></td>
<td>${p.TENCHU ?? "Không có dữ liệu"}</td>
</tr>

<tr>
<td><b>Địa chỉ</b></td>
<td>${p.DIACHI ?? ""}</td>
</tr>

</table>

</div>
`;

    document.getElementById("parcelContent").innerHTML = html;

    openPanel();

}
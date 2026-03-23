/* =========================
LOAD ĐỊA CHÍNH MỚI (MapLibre)
========================= */

function loadDcMoi(data) {
    clearMeasure();

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu địa chính mới");
        return;
    }

    // Không cần parse lại dữ liệu, sử dụng trực tiếp từ backend
    let safeData = data;

    safeData.features = safeData.features.filter(f => {
        return (
            f &&
            f.type === "Feature" &&
            f.geometry &&
            f.geometry.type &&
            f.geometry.coordinates
        );
    });

    if (!safeData.features.length) {
        console.warn("Không có feature hợp lệ");
        return;
    }

    // Kiểm tra xem layer đã tồn tại chưa, nếu có thì dùng `setData`
    const isNewSource = upsertGeoJSONSource("dc_moi", safeData);

    // Nếu là source mới, mới add các layer
    if (isNewSource) {
        /* polygon */
        map.addLayer({
            id: "dc_moi_fill",
            type: "fill",
            source: "dc_moi",
            paint: {
                "fill-color": "#ffd700",
                "fill-opacity": 0.35
            }
        });

        /* border */
        map.addLayer({
            id: "dc_moi_line",
            type: "line",
            source: "dc_moi",
            paint: {
                "line-color": "#ffd700",
                "line-width": 2
            }
        });
    }

    /* =========================
    CLICK THỬA
    ========================= */

    map.on("click", "dc_moi_fill", function (e) {
        if (!e.features || e.features.length === 0) {
            alert("Không có thửa!");
            return;
        }

        let feature = e.features[0];

        window.currentFeature = feature;
        console.log("CLICK xong currentFeature =", window.currentFeature);

        highlightParcel(feature);
        showParcelInfo(feature);
        drawParcelMeasure(feature);
    });

    map.on("dblclick", "dc_moi_fill", function (e) {
        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;
        addMarker(lat, lng);
    });

    /* =========================
    ZOOM
    ========================= */

    try {
        let bbox = safeData.bbox; // sử dụng bbox từ backend (meta)

        if (
            Array.isArray(bbox) &&
            bbox.length === 4 &&
            isFinite(bbox[0]) &&
            isFinite(bbox[1]) &&
            isFinite(bbox[2]) &&
            isFinite(bbox[3])
        ) {
            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: 20
                }
            );
        }
    } catch (e) {
        console.warn("Không thể fitBounds dc_moi:", e);
    }

    /* search nếu có */
    if (typeof initParcelSearch === "function") {
        try {
            initParcelSearch(safeData);
        } catch (e) {
            console.warn("initParcelSearch dc_moi lỗi:", e);
        }
    }
}

/* Hàm dùng chung cho việc upsert source */
function upsertGeoJSONSource(sourceId, data) {
    const source = map.getSource(sourceId);

    if (source) {
        source.setData(data); // update data thay vì xóa rồi add lại
        return false;
    }

    map.addSource(sourceId, {
        type: "geojson",
        data: data,
        tolerance: 1,
        buffer: 0
    });

    return true;
}
let currentKTT = 108.5;
let measureMarkers = [];
window.canhVisible = true;

/* =========================
VN2000
========================= */

function updateVN2000(ktt) {
    currentKTT = ktt;

    const def = `+proj=tmerc +lat_0=0 +lon_0=${ktt} +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs`;
    proj4.defs("VN2000_Current", def);
}

updateVN2000(currentKTT);

function toVN2000(coord) {
    let r = proj4(
        proj4.defs("EPSG:4326"),
        proj4.defs("VN2000_Current"),
        [coord[0], coord[1]]
    );

    return {
        x: r[1],
        y: r[0]
    };
}

function distVN2000(a, b) {
    let dx = a.x - b.x;
    let dy = a.y - b.y;
    return Math.sqrt(dx * dx + dy * dy);
}

function areaVN2000(points) {
    let area = 0;

    for (let i = 0; i < points.length; i++) {
        let j = (i + 1) % points.length;
        area += points[i].x * points[j].y;
        area -= points[j].x * points[i].y;
    }

    return Math.abs(area) / 2;
}

/* =========================
TOAST MESSAGE
========================= */

function showToast(message, type = "info", timeout = 2200) {
    let box = document.getElementById("appToast");

    if (!box) {
        box = document.createElement("div");
        box.id = "appToast";
        box.style.position = "fixed";
        box.style.top = "20px";
        box.style.right = "20px";
        box.style.zIndex = "99999";
        box.style.minWidth = "240px";
        box.style.maxWidth = "360px";
        box.style.padding = "12px 14px";
        box.style.borderRadius = "10px";
        box.style.color = "#fff";
        box.style.fontSize = "14px";
        box.style.fontWeight = "600";
        box.style.boxShadow = "0 8px 24px rgba(0,0,0,0.18)";
        box.style.transition = "all 0.25s ease";
        box.style.opacity = "0";
        box.style.transform = "translateY(-10px)";
        document.body.appendChild(box);
    }

    if (type === "success") {
        box.style.background = "#16a34a";
    } else if (type === "warning") {
        box.style.background = "#f59e0b";
    } else if (type === "error") {
        box.style.background = "#dc2626";
    } else {
        box.style.background = "#2563eb";
    }

    box.innerText = message;
    box.style.opacity = "1";
    box.style.transform = "translateY(0)";

    clearTimeout(box._hideTimer);
    box._hideTimer = setTimeout(() => {
        box.style.opacity = "0";
        box.style.transform = "translateY(-10px)";
    }, timeout);
}

/* =========================
HELPER LẤY TỌA ĐỘ THỬA
========================= */

function getParcelCoords(feature) {
    if (!feature || !feature.geometry) return [];

    let coords = [];

    if (feature.geometry.type === "MultiPolygon") {
        coords = feature.geometry.coordinates?.[0]?.[0] || [];
    } else if (feature.geometry.type === "Polygon") {
        coords = feature.geometry.coordinates?.[0] || [];
    }

    if (!Array.isArray(coords) || coords.length === 0) return [];

    if (coords.length > 1) {
        let first = coords[0];
        let last = coords[coords.length - 1];

        if (first[0] === last[0] && first[1] === last[1]) {
            coords = coords.slice(0, coords.length - 1);
        }
    }

    return coords;
}

/* =========================
MIDPOINT OFFSET NHẸ RA NGOÀI CẠNH
========================= */

function getOffsetMidpoint(coords, i, offset = 0.000006) {
    let a = coords[i];
    let b = coords[(i + 1) % coords.length];

    let midX = (a[0] + b[0]) / 2;
    let midY = (a[1] + b[1]) / 2;

    let dx = b[0] - a[0];
    let dy = b[1] - a[1];
    let len = Math.sqrt(dx * dx + dy * dy);

    if (!len) return [midX, midY];

    let nx = -dy / len;
    let ny = dx / len;

    return [
        midX + nx * offset,
        midY + ny * offset
    ];
}


/* =========================
TẠO LABEL CẠNH XOAY THEO CẠNH
========================= */

function createEdgeLabel(text, angle) {
    const wrap = document.createElement("div");
    wrap.className = "edgeMarker";

    const inner = document.createElement("div");
    inner.className = "edgeLabel";
    inner.innerText = text;

    if (angle > 90) angle -= 180;
    if (angle < -90) angle += 180;

    inner.style.transform = `rotate(${angle}deg) translateY(-10px)`;

    wrap.appendChild(inner);
    return wrap;
}

/* =========================
QUẢN LÝ HIỆN / ẨN CẠNH
========================= */

function setMeasureMarkerVisible(marker, visible) {
    if (!marker) return;

    let el = null;

    if (typeof marker.getElement === "function") {
        el = marker.getElement();
    } else if (marker._element) {
        el = marker._element;
    }

    if (el) {
        el.style.display = visible ? "" : "none";
    }
}

function syncCanhToggleState() {
    const checkbox = document.getElementById("toggle_canh");
    if (checkbox) {
        window.canhVisible = checkbox.checked;
    }
}

function toggleCanhVisibility(forceValue = null) {
    if (typeof forceValue === "boolean") {
        window.canhVisible = forceValue;
    } else {
        syncCanhToggleState();
    }

    measureMarkers.forEach(marker => {
        setMeasureMarkerVisible(marker, window.canhVisible);
    });
}

/* =========================
CLEAR MEASURE
========================= */

function clearMeasure() {
    measureMarkers.forEach(m => {
        if (m && typeof m.remove === "function") {
            m.remove();
        }
    });
    measureMarkers = [];
}

/* =========================
HIGHLIGHT THỬA
========================= */

function highlightParcel(feature) {
    if (!feature) return;

    if (map.getLayer("parcelHighlightFill")) map.removeLayer("parcelHighlightFill");
    if (map.getLayer("parcelHighlightLine")) map.removeLayer("parcelHighlightLine");
    if (map.getSource("parcelHighlight")) map.removeSource("parcelHighlight");

    map.addSource("parcelHighlight", {
        type: "geojson",
        data: feature
    });

    map.addLayer({
        id: "parcelHighlightFill",
        type: "fill",
        source: "parcelHighlight",
        paint: {
            "fill-color": "#fde047",
            "fill-opacity": 0.22
        }
    });

    map.addLayer({
        id: "parcelHighlightLine",
        type: "line",
        source: "parcelHighlight",
        paint: {
            "line-color": "#ef4444",
            "line-width": 4
        }
    });
}

/* =========================
VẼ ĐO THỬA
========================= */

function drawParcelMeasure(feature) {
    if (!feature) return;

    syncCanhToggleState();
    clearMeasure();

    let coords = getParcelCoords(feature);
    if (!coords.length) {
        console.warn("Không có tọa độ thửa");
        return;
    }

    let ptsVN = coords.map(c => toVN2000(c));

    // LABEL ĐỈNH
    coords.forEach((p, i) => {
        let el = document.createElement("div");
        el.className = "vertexLabel";
        el.innerHTML = i + 1;

        let m = new maplibregl.Marker({
            element: el,
            anchor: "center"
        })
            .setLngLat(p)
            .addTo(map);

        measureMarkers.push(m);
        setMeasureMarkerVisible(m, window.canhVisible);
    });

    let perimeter = 0;

    // LABEL CẠNH
    for (let i = 0; i < ptsVN.length; i++) {
        let p1 = ptsVN[i];
        let p2 = ptsVN[(i + 1) % ptsVN.length];

        let dist = distVN2000(p1, p2);
        perimeter += dist;

        // bỏ cạnh quá ngắn để đỡ rối
        if (dist < 4) continue;
        if (dist < 8 && i % 2 !== 0) continue;

        let a = coords[i];
        let b = coords[(i + 1) % coords.length];

        let mid = getOffsetMidpoint(coords, i, 0.00001);

        let dx = b[0] - a[0];
        let dy = b[1] - a[1];
        let angle = Math.atan2(dy, dx) * 180 / Math.PI;

        let el = createEdgeLabel(dist.toFixed(2), angle);

        let m = new maplibregl.Marker({
            element: el,
            anchor: "center"
        })
            .setLngLat(mid)
            .addTo(map);

        measureMarkers.push(m);
        setMeasureMarkerVisible(m, window.canhVisible);
    }

    // DIỆN TÍCH
    let p = feature.properties || {};
    let area = Number(p.DIENTICH || 0);
    if (!area || area <= 0) area = areaVN2000(ptsVN);

    let center = turf.centroid(feature).geometry.coordinates;

    let el = document.createElement("div");
    el.className = "areaLabel";
    el.innerHTML = `
        <div class="areaLine">Diện tích: ${area.toFixed(2)} m²</div>
        <div class="areaLine">Chu vi: ${perimeter.toFixed(2)} m</div>
    `;

    let m = new maplibregl.Marker({
        element: el,
        anchor: "center"
    })
        .setLngLat(center)
        .addTo(map);

    measureMarkers.push(m);
    setMeasureMarkerVisible(m, window.canhVisible);
}

/* =========================
XUẤT TXT TỌA ĐỘ
========================= */

function exportCoordinates() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    let feature = window.currentFeature;
    let coords = getParcelCoords(feature);

    if (!coords.length) {
        showToast("Không có tọa độ thửa", "warning");
        return;
    }

    let text = "X(m)\tY(m)\tZ(m)\n";

    coords.forEach((c) => {
        let vn = toVN2000(c);
        let x = vn.x.toFixed(3);
        let y = vn.y.toFixed(3);
        let z = 0;

        text += `${x}\t${y}\t${z}\n`;
    });

    let blob = new Blob([text], { type: "text/plain;charset=utf-8;" });
    let url = URL.createObjectURL(blob);

    let a = document.createElement("a");
    a.href = url;

    let p = feature.properties || {};
    a.download = `To_${p.SHBANDO || ""}_Thua_${p.SHTHUA || ""}.txt`;

    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    URL.revokeObjectURL(url);
    showToast("Đã xuất file tọa độ.", "success");
}

/* =========================
XUẤT PDF
========================= */

async function exportParcelPDF() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    if (!window.jspdf || !window.jspdf.jsPDF) {
        showToast("Chưa nạp thư viện jsPDF!", "error");
        console.error("window.jspdf không tồn tại");
        return;
    }

    let feature = window.currentFeature;
    let coords = getParcelCoords(feature);

    if (!coords.length) {
        showToast("Không có tọa độ thửa để xuất PDF", "warning");
        return;
    }

    let p = feature.properties || {};
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF("p", "mm", "a4");

    const pageWidth = 210;
    const pageHeight = 297;
    const margin = 10;
    const contentWidth = pageWidth - margin * 2;

    let y = 12;

    function line(yPos) {
        doc.line(margin, yPos, pageWidth - margin, yPos);
    }

    function rect(x, y, w, h) {
        doc.rect(x, y, w, h);
    }

    function cell(x, y, w, h, text, align = "left") {
        doc.rect(x, y, w, h);

        let tx = x + 2;
        if (align === "center") tx = x + w / 2;
        if (align === "right") tx = x + w - 2;

        doc.text(String(text ?? ""), tx, y + 5, {
            align: align
        });
    }

    function addNewPageWithHeader() {
        doc.addPage();
        y = 12;

        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text("BANG TOA DO THUA DAT", pageWidth / 2, y, { align: "center" });
        y += 8;

        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
    }

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("THONG TIN THUA DAT", pageWidth / 2, y, { align: "center" });
    y += 8;

    doc.setFont("helvetica", "normal");
    doc.setFontSize(10);
    doc.text("Trich xuat thong tin va toa do VN2000", pageWidth / 2, y, { align: "center" });
    y += 8;

    line(y);
    y += 6;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(12);
    doc.text("1. THONG TIN CHUNG", margin, y);
    y += 4;

    const infoTop = y;
    const infoHeight = 34;
    rect(margin, infoTop, contentWidth, infoHeight);

    doc.setFont("helvetica", "normal");
    doc.setFontSize(11);

    doc.text(`To ban do: ${p.SHBANDO || ""}`, margin + 4, infoTop + 7);
    doc.text(`Thua dat: ${p.SHTHUA || ""}`, 110, infoTop + 7);

    doc.text(`To cu: ${p.SOTOCU || ""}`, margin + 4, infoTop + 14);
    doc.text(`Dien tich: ${p.DIENTICH || ""} m2`, 110, infoTop + 14);

    doc.text(`Loai dat: ${p.KHLOAIDAT || ""}`, margin + 4, infoTop + 21);
    doc.text(`Chu su dung: ${p.TENCHU || ""}`, margin + 4, infoTop + 28);

    y = infoTop + infoHeight + 8;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(12);
    doc.text("2. BANG TOA DO VN2000", margin, y);
    y += 6;

    const col1 = 18;
    const col2 = 80;
    const col3 = 80;
    const rowH = 8;

    doc.setFontSize(10);
    cell(margin, y, col1, rowH, "STT", "center");
    cell(margin + col1, y, col2, rowH, "X (m)", "center");
    cell(margin + col1 + col2, y, col3, rowH, "Y (m)", "center");
    y += rowH;

    doc.setFont("helvetica", "normal");

    coords.forEach((c, i) => {
        let vn = toVN2000(c);

        if (y + rowH > pageHeight - 15) {
            addNewPageWithHeader();

            doc.setFont("helvetica", "bold");
            cell(margin, y, col1, rowH, "STT", "center");
            cell(margin + col1, y, col2, rowH, "X (m)", "center");
            cell(margin + col1 + col2, y, col3, rowH, "Y (m)", "center");
            y += rowH;
            doc.setFont("helvetica", "normal");
        }

        cell(margin, y, col1, rowH, i + 1, "center");
        cell(margin + col1, y, col2, rowH, vn.x.toFixed(3), "right");
        cell(margin + col1 + col2, y, col3, rowH, vn.y.toFixed(3), "right");
        y += rowH;
    });

    if (window.html2canvas) {
        try {
            const mapEl = document.getElementById("map");

            if (mapEl) {
                doc.addPage();
                y = 12;

                doc.setFont("helvetica", "bold");
                doc.setFontSize(14);
                doc.text("3. HINH BAN DO THUA DAT", pageWidth / 2, y, { align: "center" });
                y += 10;

                const canvas = await html2canvas(mapEl, {
                    useCORS: true,
                    backgroundColor: "#ffffff",
                    scale: 1
                });

                const imgData = canvas.toDataURL("image/png");

                let imgW = 190;
                let imgH = canvas.height * imgW / canvas.width;

                if (imgH > 250) {
                    imgH = 250;
                    imgW = canvas.width * imgH / canvas.height;
                }

                let imgX = (pageWidth - imgW) / 2;
                doc.addImage(imgData, "PNG", imgX, y, imgW, imgH);
            }
        } catch (err) {
            console.error("Loi chup ban do:", err);
        }
    }

    let filename = `To_${p.SHBANDO || ""}_Thua_${p.SHTHUA || ""}.pdf`;
    doc.save(filename);
    showToast("Đã xuất PDF.", "success");
}

/* =========================
GOOGLE MAPS
========================= */

function openParcelGoogleMaps() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    let feature = window.currentFeature;

    if (!feature.geometry) {
        showToast("Thửa không có dữ liệu hình học!", "warning");
        return;
    }

    let center;

    try {
        center = turf.centroid(feature).geometry.coordinates;
    } catch (err) {
        console.error("Không tính được tâm thửa:", err);
        showToast("Không lấy được vị trí thửa!", "error");
        return;
    }

    if (!center || center.length < 2) {
        showToast("Không lấy được tọa độ thửa!", "error");
        return;
    }

    let lng = center[0];
    let lat = center[1];

    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    window.open(googleMapsUrl, "_blank");
}
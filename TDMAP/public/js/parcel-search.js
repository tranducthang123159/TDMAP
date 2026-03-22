/* =========================
PARCEL SEARCH SYSTEM
========================= */

let parcelList = [];

/* LOAD DATA */

function initParcelSearch(data){

    parcelList = data.features;

    renderParcelList();

    initParcelEvents();
}


/* RENDER LIST */

function renderParcelList(){

    let html = "";

    parcelList.forEach((f,i)=>{

        let p = f.properties;

        html += `
        <div class="parcel-row" onclick="selectParcel(${i})">

            <div><b>Tờ mới:</b> ${p.SHBANDO ?? ""}</div>
            <div><b>Tờ cũ:</b> ${p.SOTOCU ?? ""}</div> <!-- ✅ -->
            <div><b>Thửa:</b> ${p.SHTHUA ?? ""}</div>
            <div><b>Chủ:</b> ${p.TENCHU ?? ""}</div>

        </div>
        `;

    });

    document.getElementById("parcelList").innerHTML = html;
}


/* FILTER */

function filterParcel(){

    let to = document.getElementById("searchTo").value.toLowerCase();
    let tocu = document.getElementById("searchToCu").value.toLowerCase(); // ✅
    let thua = document.getElementById("searchThua").value.toLowerCase();
    let chu = document.getElementById("searchChu").value.toLowerCase();

    let html = "";

    parcelList.forEach((f,i)=>{

        let p = f.properties;

        let match =
        (!to || (p.SHBANDO+"").toLowerCase().includes(to)) &&
        (!tocu || (p.SOTOCU+"").toLowerCase().includes(tocu)) && // ✅
        (!thua || (p.SHTHUA+"").toLowerCase().includes(thua)) &&
        (!chu || (p.TENCHU ?? "").toLowerCase().includes(chu));

        if(match){

            html += `
            <div class="parcel-row" onclick="selectParcel(${i})">

                <div><b>Tờ mới:</b> ${p.SHBANDO ?? ""}</div>
                <div><b>Tờ cũ:</b> ${p.SOTOCU ?? ""}</div>
                <div><b>Thửa:</b> ${p.SHTHUA ?? ""}</div>
                <div><b>Chủ:</b> ${p.TENCHU ?? ""}</div>

            </div>
            `;

        }

    });

    document.getElementById("parcelList").innerHTML = html;
}


/* EVENTS */

function initParcelEvents(){

    document.getElementById("searchTo")
    .addEventListener("keyup",filterParcel);

    document.getElementById("searchToCu") // ✅
    .addEventListener("keyup",filterParcel);

    document.getElementById("searchThua")
    .addEventListener("keyup",filterParcel);

    document.getElementById("searchChu")
    .addEventListener("keyup",filterParcel);
}


/* SELECT PARCEL */
function selectParcel(i){

    let feature = parcelList[i];

    highlightParcel(feature);

    showParcelInfo(feature); // ✅ FIX

    drawParcelMeasure(feature);

    let bbox = turf.bbox(feature);

    map.fitBounds([
        [bbox[0],bbox[1]],
        [bbox[2],bbox[3]]
    ],{
        padding:40
    });

}


/* TOGGLE UPLOAD */

function toggleUpload(){

    let body = document.getElementById("uploadBody");
    let arrow = document.getElementById("uploadArrow");

    body.classList.toggle("hide");

    if(body.classList.contains("hide")){
        arrow.innerHTML="▲";
    }else{
        arrow.innerHTML="▼";
    }

}


/* TOGGLE PARCEL PANEL */

function toggleParcelPanel(){

    let body = document.getElementById("parcelBody");
    let arrow = document.getElementById("parcelArrow");

    body.classList.toggle("collapsed");

    if(body.classList.contains("collapsed")){
        arrow.innerHTML="▲";
    }else{
        arrow.innerHTML="▼";
    }

}
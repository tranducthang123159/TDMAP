self.onmessage = function(e){

let file = e.data;

let reader = new FileReader();

reader.onload = function(evt){

let geo = JSON.parse(evt.target.result);

postMessage(geo);

};

reader.readAsText(file);

};
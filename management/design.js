function toggleProfile(){

    let box = document.getElementById("profileBox");

    if(box.style.display === "block"){
        box.style.display = "none";
    }else{
        box.style.display = "block";
    }

}

function toggleRefnum() {
    const method = document.getElementById('payment-option').value;
    const ref_group = document.getElementById('ref_group');
    const ref = document.getElementById('refnum');
    if (method === "GCash" || method === "Maya" || method === "MariBank") {
        ref_group.style.display = 'block';   
        ref.required = true;           
    } else {
        ref_group.style.display = 'none';    
        ref.required = false;          
    }
}
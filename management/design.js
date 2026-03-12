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



function deleteEmp(accID) {
    if (confirm('Are you sure you want to delete this employee?')) {
        fetch('management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'accID=' + accID
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Employee deleted successfully!');
                location.reload(); 
            } else {
                alert('Error deleting employee: ' + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting employee');
        });
    }
}



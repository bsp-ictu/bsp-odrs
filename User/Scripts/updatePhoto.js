function openUploadPhotoForm(){
    document.getElementById('uploadPhoto').style.display = 'block';
}

function closeUploadPhotoForm(){
    document.getElementById('uploadPhoto').style.display = 'none';
}

document.getElementById('imageFileInput').addEventListener("change", function(){
    let fileInput = document.getElementById('imageFileInput').files[0]; 

    if ((fileInput.type == "image/jpeg") || (fileInput.type == "image/jpg") || (fileInput.type == "image/png")){
        let fileName = fileInput.name;

        let fileSize = "";
        if (fileInput.size < 1e3 ){
            fileSize = `${fileInput.size} bytes`;
        } else if ((fileInput.size >= 1e3) && (fileInput.size < 1e6)){
            fileSize = `${(fileInput.size / 1e3).toFixed(1)} KB`;
        } else {
            fileSize = `${(fileInput.size / 1e6).toFixed(1)} MB`;
        }

        document.getElementById('fileName').value = fileName;
        document.getElementById('fileSize').value = fileSize;
    } else {
        document.getElementById('fileName').value = "Invalid file type input";
        document.getElementById('fileSize').value = "Invalid file type input";
    }
});
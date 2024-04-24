<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test file upload</title>
</head>
<body>
    <form onsubmit="handle_sumit(this, event)" method="post" enctype="multipart/form-data">
        <input type="file" id="images_elem" name="images" id="" multiple>
        <button type="submit">Submit</button>
    </form>
</body>
    <script>
       function handle_sumit(elem, event){
        console.log(images_elem.files);
        event.preventDefault();
        const formdata = new FormData();
        formdata.append('images[]', images_elem.files);
   
        fetch(`http://127.0.0.1:8000/api/update_vendor_images`, {
            method: "post",
            headers: {
                'bearer': "23|e03V65lmcUrLCAjH4XX1SfJu6Ew2hVBWDyhnz6zf"
            },
            body: formdata
        }).then(response => response.text()).then(res => console.log(res));
       }
    </script>

</html>
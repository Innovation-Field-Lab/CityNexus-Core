@if($property->images->count() > 0)
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Images</span>
    </div>
    <div class="panel-body">
        <div class="list-group">
            @foreach($property->images as $image)
                <div class="list-group-item" onclick="showImage({{$image->id}})" style="cursor: pointer"><i class="fa fa-image"></i> {{$image->caption}} ({{$image->created_at->diffForHumans()}})</div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('js_footer')


<script>
    function addImage()
    {
        var title = 'Add Image';
        var uploader = "<form action='{{action('\CityNexus\CityNexus\Http\ImageController@postUpload')}}' method='post' enctype='multipart/form-data'>'" +
                '{!! csrf_field() !!}' +
                "<input type='hidden' name='property_id' value='{{$property->id}}'>" +
                "<input type='file' name='image'>" +
                "<label for='caption'>Caption</label>" +
                "<input class='form-control' type='text' name='caption' required>" +
                "<label for='description'>Description</label>" +
                "<textarea class='form-control' name='description'></textarea>" +
                "<br><br><input class='btn btn-primary' type='submit' value='Upload Image'>";
        triggerModal(title, uploader);
    }

    var crypto = require('crypto');

    // This is the entry function that produces data for the frontend
    // config is hash of S3 configuration:
    // * bucket
    // * region
    // * accessKey
    // * secretKey
    function s3Credentials(config, filename) {
        return {
            endpoint_url: "https://" + config.bucket + ".s3.amazonaws.com",
            params: s3Params(config, filename)
        }
    }

    // Returns the parameters that must be passed to the API call
    function s3Params(config, filename) {
        var credential = amzCredential(config);
        var policy = s3UploadPolicy(config, filename, credential);
        var policyBase64 = new Buffer(JSON.stringify(policy)).toString('base64');
        return {
            key: filename,
            acl: 'public-read',
            success_action_status: '201',
            policy: policyBase64,
            'x-amz-algorithm': 'AWS4-HMAC-SHA256',
            'x-amz-credential': credential,
            'x-amz-date': dateString() + 'T000000Z',
            'x-amz-signature': s3UploadSignature(config, policyBase64, credential)
        }
    }

    function dateString() {
        var date = new Date().toISOString();
        return date.substr(0, 4) + date.substr(5, 2) + date.substr(8, 2);
    }

    function amzCredential(config) {
        return [config.accessKey, dateString(), config.region, 's3/aws4_request'].join('/')
    }

    // Constructs the policy
    function s3UploadPolicy(config, filename, credential) {
        return {
            // 5 minutes into the future
            expiration: new Date((new Date).getTime() + (5 * 60 * 1000)).toISOString(),
            conditions: [
                { bucket: config.bucket },
                { key: filename },
                { acl: 'public-read' },
                { success_action_status: "201" },
                // Optionally control content type and file size
                // {'Content-Type': 'application/pdf'},
                ['content-length-range', 0, 1000000],
                { 'x-amz-algorithm': 'AWS4-HMAC-SHA256' },
                { 'x-amz-credential': credential },
                { 'x-amz-date': dateString() + 'T000000Z' }
            ],
        }
    }

    function hmac(key, string) {
        var hmac = require('crypto').createHmac('sha256', key);
        hmac.end(string);
        return hmac.read();
    }

    // Signs the policy with the credential
    function s3UploadSignature(config, policyBase64, credential) {
        var dateKey = hmac('AWS4' + config.secretKey, dateString());
        var dateRegionKey = hmac(dateKey, config.region);
        var dateRegionServiceKey = hmac(dateRegionKey, 's3');
        var signingKey = hmac(dateRegionServiceKey, 'aws4_request');
        return hmac(signingKey, policyBase64).toString('hex');
    }

    module.exports = {
        s3Credentials: s3Credentials
    }

    // Requires jQuery and blueimp's jQuery.fileUpload

    // Configuration
    var bucket = 'browser-upload-demo';
    // client-side validation by fileUpload should match the policy
    // restrictions so that the checks fail early
    var acceptFileType = /.*/i;
    var maxFileSize = 1000000;
    // The URL to your endpoint that maps to s3Credentials function
    var credentialsUrl = '/s3_credentials';

    window.initS3FileUpload = function($fileInput) {
        $fileInput.fileupload({
            acceptFileTypes: acceptFileType,
            maxFileSize: maxFileSize,
            url: 'https://' + bucket + '.s3.amazonaws.com',
            paramName: 'file',
            add: s3add,
            dataType: 'xml',
            done: onS3Done
        });
    };

    // This function retrieves s3 parameters from our server API and appends them
    // to the upload form.
    function s3add(e, data) {
        var filename = data.files[0].name;
        var params = [];
        $.ajax({
            url: credentialsUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                filename: filename
            },
            success: function(s3Data) {
                data.formData = s3Data.params;
                data.submit();
            }
        });
        return params;
    };

    // Example of extracting information about the uploaded file
    // Typically, after uploading a file to S3, you want to register that file with
    // your backend. Remember that we did not persist anything before the upload.
    function onS3Done(e, data) {
        var s3Url = $(data.jqXHR.responseXML).find('Location').text();
        var s3Key = $(data.jqXHR.responseXML).find('Key').text();
    };

    function showImage(id)
    {
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ImageController@getShow')}}/' + id,
        }).success(function(data){
            var image = '<a href="' + data.source + '" target="_blank"><img style="max-width: 90%" class="model_image" src="' + data.source + '"/></a>'+
                    @can('citynexus', ['property', 'delete'])
                    '<br><a class="pull-right" href="/citynexus/image/delete/' + id + '">' +
                    '<i class="fa fa-trash"></i> </a>' +
                    @endcan
                '<p>' + data.description + '</p>';
            triggerModal(data.caption, image);

        });
    }

</script>


@endpush

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mail Template</title>
</head>
<style>
    /* font style */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    * {
        margin: 0px;
        padding: 0px;
        box-sizing: border-box;
        font-family: 'Poppins';
    }

    td {
        display: block;
    }

    h3 {
        font-size: 1rem;
        text-align: center;
    }
</style>

<body>
    <div class="main-container" style="max-width: 45rem; margin: auto;">
        <table class="form-container" style="max-width: 100%; border: 1px solid gray; margin: 0 auto; padding: 2rem 0rem;">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img src="{{asset('images/lead_notify_hero_banner.png')}}" alt="" class="logo" style="width: 40%; margin:auto;  margin: 1rem auto; display: block;">
                            </td>
                        </tr>
                        <tr>
                            <td class="lead-header" style="max-width: 100%; padding: 5%; background: #FFEEEE; display: block; margin-bottom: 1rem; text-align: center;">
                                <h3 style="font-weight: 600; margin-bottom: .5rem;">Thank you for signing up on our website! We're thrilled to have you join our community. Our team is currently reviewing your information and will be reaching out to you shortly to connect and ensure everything is in place.</h3>
                            </td>
                        </tr>
                        <tr>
                            <td class="lead-details " style="border: 1px solid black;  max-width:30rem; padding: 1rem 3rem;  margin: 1rem auto;">
                                <table style="width: 100%">
                                    @foreach ($data as $key => $value)
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">{{ucwords(str_replace("_", " ", $key))}} :</h3>
                                            <p style="display: inline; float: right;">{{$value}}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="lead-footer" style="padding: 0rem 2rem; text-align: center;">
                                <h4 class="p-400" style="font-weight: 400;">If you have any questions or need immediate assistance, feel free to contact us at 18008890082.</h4>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
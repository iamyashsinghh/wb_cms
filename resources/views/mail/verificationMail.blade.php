<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Card</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins";
    }

    body {
        font-size: 90%;
    }

    .gap {
        margin-bottom: 2rem;
    }

    h3 {
        font: 1rem;
    }

    h2 {
        font-size: 1.5rem;
    }
</style>

<body>
    <table style="box-shadow: 1px 1px 5px gray; max-width: 35rem;  margin: auto; border-radius: 4rem; overflow: hidden; display: block; border: 3px solid black;">
        <tr>
            <td>
                <table style="border-radius: 4rem; overflow: hidden; ">
                    <tr>
                        <td class="card-header " style="width: 35rem;">
                            <img src="{{asset('images/otp_hero_banner.png')}}" alt="" style="height: 15rem; width: 100%;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table style="margin-top: -40px;">
                    <tr>
                        <td class="template-content" style=" padding: 0rem 8% 1rem 5%;">
                            <h3 class="gap">Hello {{$data['name']}}</h3>
                            <h3 class="gap">Welcome to Wedding Banquets</h3>
                            <h3 class="gap">Here is your confirmation code to verfiy it's you.</h3>
                            <h2 class="gap">USE CODE <span class="otp" style="color: #870808;">{{$data['otp']}}</span></h2>
                            <h3 class="gap">The code expires in 10 minutes</h3>
                            <h3 class="gap">You can also get in touch with our team at 18008890082</h3>
                            <h3 class="gap">We appreciate your interest in Wedding Banquets.</h3>
                            <div class="footer">
                                <h3>Thanks & Regards,</h3>
                                <h3>Wedding Banquets Team ....</h3>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
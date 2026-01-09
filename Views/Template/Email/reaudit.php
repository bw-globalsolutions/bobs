<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Improvement Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .letter-heading {
            text-align: center;
        }
        .letter-heading h1 {
            margin: 0;
        }
        .letter-content {
            margin-top: 20px;
        }
        .letter-content p {
            margin: 10px 0;
        }
        .letter-signature {
            margin-top: 40px;
            font-weight: bold;
        }
        .letter-footer {
            font-size: 0.9em;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="letter-heading">
        <h1>Operational Improvement Letter</h1>
        <p>ID: 62949</p>
    </div>

    <div class="letter-content">
        <p><strong><?=date("F j, Y")?></strong></p>

        <p><strong><?=$data['address_1']?> </strong></p>
        <p><strong> #<?=$data['numero_tienda']?> </strong></p>

        <p>Ref.: Notice to Franchisee of Necessary Improvements no. 55777</p>

        <p>Dear Franchisee:</p>

        <p>ARGUILEA recently completed a PRIDE Visit for American Corporation (“<b><?=$data['franchissees_name']?></b>”) at restaurant no. <b><?=$data['numero_tienda']?></b>, located at <b><?=$data['address_1']?></b> on <b><?=$data['date_visit']?></b>.</p>

        <p>Franchisees are required to operate their restaurants in accordance with Franchisor’s standards as outlined in the System Standards and Operations Manual and PRIDE Standards of Performance (“System Standards”). </p>
        
        <p>During the PRIDE Visit, ARGUILEA identified areas of operational performance in which improvement is required to meet said System Standards. Specific details of the operational performance deficiencies identified in the PRIDE Visit were provided to you by email and through the ARGUILEA portal, accessible through The Feed.</p>
        
        <p>Franchisor wishes to provide you with the opportunity to remedy the operational performance deficiencies that ARGUILEA observed during the last visit. ARGUILEA will make another visit within the next fifteen (15) to forty-five (45) days to verify that the operational deficiencies of your restaurant are corrected.</p>

        <p>If the deficiencies are not corrected by the time of ARGUILEA’s follow-up visit, your case may be referred to the Franchisor’s Operations Team and the Legal Department.</p>

        <p>If you have any questions regarding this notice, the System Standards, or the Restaurant PRIDE Visit report, please contact your territory representative – Area Manager or Director of Operations.</p>

        <p>DC:</p>

        <div class="letter-signature">
            <p><strong>Franchise Owner: Email Address?</strong></p>
            <p>(You need to keep track of the name of this email so you know who it will come from)</p>
        </div>
    </div>



</body>
</html>

<?
// Get the right color
switch ( $goldColor )
{
    case "GG":
    {
        $goldColor = "Gelbgold";
    }
    break;
    case "GR":
    {
        $goldColor = "Gelb- + Rotgold";
    }
    break;

    case "GW":
    {
        $goldColor = "Gelb- + Wei�gold";
    }
    break;
                
    case "PF":
    {
        $goldColor = "Platin + Feingold";
    }
    break;

    case "PG":
    {
        $goldColor = "Platin+750/Gold";
    }
    break;

    case "PT":
    {
        $goldColor = "Platin";
    }
    break;

    case "RG":
    {
        $goldColor = "Rotgold";
    }
    break;

    case "RW":
    {
        $goldColor = "Rot- und Wei�gold";
    }
    break;

    case "TR":
    {
        $goldColor = "Gold Tricolor";
    }
    break;

    case "WG":
    {
        $goldColor = "Wei�gold";
    }
    break;

    default:
    {
        if ( $goldColor )
            eZLog::writeWarning( "Product: " . $productNumber . ". Could not found a goldcolor case for: " . $goldColor );
    }
        break;
}
?>

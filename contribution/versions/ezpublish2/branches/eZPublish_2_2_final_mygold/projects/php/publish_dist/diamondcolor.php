<?php


switch( $dia1Color )
{
    case "D":
    {
        $dia1Color = "River (hochfeines Wei�+)";
    }
    break;
    case "E":
    {
        $dia1Color = "River (hochfeines Wei�)";
    }
    break;

    case "F":
    {
        $dia1Color = "Top Wesselton (feines Wei�+)";
    }
    break;

    case "G":
    {
        $dia1Color = "Top Wesselton (feines Wei�)";
    }
    break;

    case "H":
    {
        $dia1Color = "Wesselton (Wei�)";
    }
    break;

    case "I":
    {
        $dia1Color = "Top Crystal (schwach get�ntes Wei�)";
    }
    break;

    case "J":
    {
        $dia1Color = "Crystal (get�ntes Wei�)";
    }
    break;

    default:
    {
        if ( $dia1Color )
            eZLog::writeWarning( "Product: " . $productNumber . ". Could not found a dia1Color case for: " . $dia1Color  );
    }
    break;
}

switch( $dia2Color )
{
    case "D":
    {
        $dia2Color = "River (hochfeines Wei�+)";
    }
    break;
    case "E":
    {
        $dia2Color = "River (hochfeines Wei�)";
    }
    break;

    case "F":
    {
        $dia2Color = "Top Wesselton (feines Wei�+)";
    }
    break;

    case "G":
    {
        $dia2Color = "Top Wesselton (feines Wei�)";
    }
    break;

    case "H":
    {
        $dia2Color = "Wesselton (Wei�)";
    }
    break;

    case "I":
    {
        $dia2Color = "Top Crystal (schwach get�ntes Wei�)";
    }
    break;

    case "J":
    {
        $dia2Color = "Crystal (get�ntes Wei�)";
    }
    break;

    default:
    {
        if ( $dia2Color )
        {
            eZLog::writeWarning( "Product: " . $productNumber . ". Could not found a dia2Color case for: " . $dia2Color );
        }
    }
    break;
}

?>

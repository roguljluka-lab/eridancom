<?php

$output = '<div class="dc-trip">';

if($putovanje->type == 'ostala') {

    $output .= '<form method="get" action="' . get_permalink($dc_settings->stranica_rezervacije) . '">';
    $output .= '<input type="hidden" name="korak" value="1">';
    $output .= '<input type="hidden" name="pid" value="' . $putovanje->id . '">';

    $output .= '<table style="width: 100%;padding-top: 1em;" class="dc_table_st">
    <tr>
        <td>Šifra putovanja </td>
        <td style="text-align: right;"><b>' . esc_html($putovanje->sifra) . '</b></td>
    </tr>
    <tr>
        <td>Iznos za gotovinu </td>
        <td style="text-align: right;"><b>' . esc_html($putovanje->ukupni_iznos) . ' €</b></td>
    </tr>
    <tr>
        <td>Iznos za kartice </td>
        <td style="text-align: right;"><b>' . esc_html($putovanje->ukupni_iznos_kartica) . ' €</b></td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 0.8em;">** Cijene su iskazane po putniku.</td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td>Datum polaska</td>
        <td style="text-align: right;"><b>' . date("d.m.Y.", strtotime($putovanje->putovanje_od)) . '</b></td>
    </tr>
    <tr>
        <td>Datum povraka</td>
        <td style="text-align: right;"><b>' . date("d.m.Y.", strtotime($putovanje->putovanje_do)) . '</b></td>
    </tr>
   
    <tr>
        <td colspan="2"><br></td>
    </tr>
</table>';
    $output .= '<button class="nd_travel_width_100_percentage nd_travel_border_width_0 nd_travel_cursor_pointer nd_travel_margin_top_10 nd_travel_font_family_poppins nd_travel_letter_spacing_1_important" type="submit" style="font-size: 16px;">REZERVIRAJ</a>';

    $output .= '</form>';
} else {

    $output .= '<div style="padding-top: 6em;">Ovo putovanje je dostupno samo na upit.<br>Hvala na razumijevanju.</div>';

}

$output .= '</div>';

return $output;



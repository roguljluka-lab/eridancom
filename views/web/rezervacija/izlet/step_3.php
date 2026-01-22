<?php // IZLETI
$settings = (new Admin())->get_settings(); // IZLETI
?> <!-- IZLETI -->
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id="reservation_form"> <!-- IZLETI -->
    <input type="hidden" name="store_new_reservation" value="true"> <!-- IZLETI -->
    <input type="hidden" name="nacin_placanja" id="nacin_placanja" value="1"> <!-- IZLETI -->
    <input type="hidden" name="ukupni_iznos_putovanja" id="ukupni_iznos_putovanja" value="<?php echo number_format($putovanje->ukupni_iznos, '2', '.', ''); ?>"> <!-- IZLETI -->
    <input type="hidden" id="totalAdditionals" value="0"> <!-- IZLETI -->
    <input type="hidden" id="totalPassengers" value="1"> <!-- IZLETI -->
    <div class="dc-row"> <!-- IZLETI -->
        <div class="dc-col-12 dc-px-0 dc-shadow"> <!-- IZLETI -->
            <div class="dc-row"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <h4 class="elementor-heading-title elementor-size-default dc-underline"> <!-- IZLETI -->
                        <span> <!-- IZLETI -->
                            Način plaćanja <!-- IZLETI -->
                        </span> <!-- IZLETI -->
                    </h4> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <div class="payment-type set_bg_color" data-nacin="1" data-iznos-putovanja="<?php echo number_format($putovanje->ukupni_iznos, '2', '.', ''); ?>" data-za-naplatu="<?php echo number_format($putovanje->ukupni_iznos, '2', '.', ''); ?>"> <!-- IZLETI -->
                        <div class="payment-title selected"> <!-- IZLETI -->
                            <span class="icon dc-icon-nonselected" style="display: none;"> <!-- IZLETI -->
                                <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <!-- IZLETI -->
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <!-- IZLETI -->
                                        <g id="ic_fluent_checkbox_unchecked_24_regular" fill="#6e6e6e" fill-rule="nonzero"> <!-- IZLETI -->
                                            <path d="M5.75,3 L18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 Z M5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z" id="🎨Color"></path> <!-- IZLETI -->
                                        </g> <!-- IZLETI -->
                                    </g> <!-- IZLETI -->
                                </svg> <!-- IZLETI -->
                            </span> <!-- IZLETI -->
                            <span class="icon dc-icon-selected"> <!-- IZLETI -->
                                <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <!-- IZLETI -->
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <!-- IZLETI -->
                                        <g id="ic_fluent_checkbox_checked_24_regular" fill="#1ca8e1" fill-rule="nonzero"> <!-- IZLETI -->
                                            <path d="M18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 L18.25,3 Z M18.25,4.5 L5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z M10,14.4393398 L16.4696699,7.96966991 C16.7625631,7.6767767 17.2374369,7.6767767 17.5303301,7.96966991 C17.7965966,8.23593648 17.8208027,8.65260016 17.6029482,8.94621165 L17.5303301,9.03033009 L10.5303301,16.0303301 C10.2640635,16.2965966 9.84739984,16.3208027 9.55378835,16.1029482 L9.46966991,16.0303301 L6.46966991,13.0303301 C6.1767767,12.7374369 6.1767767,12.2625631 6.46966991,11.9696699 C6.73593648,11.7034034 7.15260016,11.6791973 7.44621165,11.8970518 L7.53033009,11.9696699 L10,14.4393398 L16.4696699,7.96966991 L10,14.4393398 Z" id="🎨Color"></path> <!-- IZLETI -->
                                        </g> <!-- IZLETI -->
                                    </g> <!-- IZLETI -->
                                </svg> <!-- IZLETI -->
                            </span> <!-- IZLETI -->
                            <span class="title">INTERNET BANKARSTVOM - UPLATA CIJELOG IZNOSA</span> <!-- IZLETI -->
                        </div> <!-- IZLETI -->
                        <div class="payment-desc dc-small"> <!-- IZLETI -->
                            Plaćanje putem internet bankarstva ili općom uplatnicom na osnovi ponude koju ćete dobiti na e-mail u potvrdi ove rezervacije. <!-- IZLETI -->
                        </div> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row dc-my-4"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <div class="dc-summary"> <!-- IZLETI -->
                        <table style="width: 100%; line-height: 1.5;"> <!-- IZLETI -->
                            <tbody> <!-- IZLETI -->
                            <tr> <!-- IZLETI -->
                                <td class="dc-end">Iznos izleta</td> <!-- IZLETI -->
                                <td class="dc-end total"><span class="dc_iznos_putovanja"><?php echo number_format($putovanje->ukupni_iznos, '2', '.', ''); ?> €</span></td> <!-- IZLETI -->
                            </tr> <!-- IZLETI -->
                            <tr> <!-- IZLETI -->
                                <th class="dc-end"><strong>ZA NAPLATU</strong></th> <!-- IZLETI -->
                                <th class="dc-end total_za_naplatu"><strong><span class="dc_za_naplatu"><?php echo number_format($putovanje->ukupni_iznos, '2', '.', ''); ?> €</span></strong></th> <!-- IZLETI -->
                            </tr> <!-- IZLETI -->
                            </tbody> <!-- IZLETI -->
                        </table> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-col-12"> <!-- IZLETI -->
                <div class="dc-scrollbox"> <!-- IZLETI -->
                    <?php echo $settings->dc_postavke->opci_uvjeti ? nl2br($settings->dc_postavke->opci_uvjeti) : ''; ?> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row dc-my-4 dc-mt-3"> <!-- IZLETI -->
                <div class="dc-col-12 dc-mt-3"> <!-- IZLETI -->
                    <label class="dc-chck-custom-checkbox" for="privola_1"> <!-- IZLETI -->
                        <input type="checkbox" class="validate" id="privola_1"> <!-- IZLETI -->
                        <span class="dc-chck-checkmark"></span> <!-- IZLETI -->
                        <span class="dc-chck-label-text">Dajem privolu na obradu osobnih podataka u svrhu izvršenja usluge koju naručujem. *</span> <!-- IZLETI -->
                    </label> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <label class="dc-chck-custom-checkbox" for="privola_3"> <!-- IZLETI -->
                        <input type="checkbox" class="validate" id="privola_3"> <!-- IZLETI -->
                        <span class="dc-chck-checkmark"></span> <!-- IZLETI -->
                        <span class="dc-chck-label-text">Pročitao/la sam i slažem se sa <a href="https://eridan.hr/opci-uvjeti/" target="_blank">općim uvjetima korištenja</a> web stranice eridan.hr. *</span> <!-- IZLETI -->
                    </label> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <label class="dc-chck-custom-checkbox" for="privola_2"> <!-- IZLETI -->
                        <input type="checkbox" id="privola_2"> <!-- IZLETI -->
                        <span class="dc-chck-checkmark"></span> <!-- IZLETI -->
                        <span class="dc-chck-label-text">Suglasan sam da se foto/video/audio snimke polaznika mogu koristiti u objavama Agencije putem web stranice i društvenih mreža.</span> <!-- IZLETI -->
                    </label> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row mandatory" style="display: none;"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <p class="dc-alert dc-alert-danger">Polja označena crvenom bojom su obavezna. Molimo da ih popunite.</p> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row mandatory_scroll" style="display: none;"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <p class="dc-alert dc-alert-danger">Za nastavak morate pročitati opće uvjete putovanja do kraja.</p> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row mt-4"> <!-- IZLETI -->
                <div class="dc-col-5 dc-mb-3"> <!-- IZLETI -->
                    <a href="<?php echo get_permalink(); ?>?act=odustani" class="dc-button dc-button-warning" id="odustani_btn">Odustani</a> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-7"> <!-- IZLETI -->
                    <a class="dc-button dc-button-info" id="reservation_button" style="width: 100%;"> <!-- IZLETI -->
                        REZERVIRAJ <!-- IZLETI -->
                    </a> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
        </div> <!-- IZLETI -->
    </div> <!-- IZLETI -->
</form> <!-- IZLETI -->

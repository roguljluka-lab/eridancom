<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id="reservation_form">
    <?php $totalPassengers = !empty($_SESSION['dc_first_step_data']['ime_dodatni']) ? 2 : 1; ?>
    <input type="hidden" name="store_new_reservation" value="true">
    <input type="hidden" name="nacin_placanja" id="nacin_placanja" value="3">
    <input type="hidden" name="ukupni_iznos_putovanja" id="ukupni_iznos_putovanja" value="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>">
    <input type="hidden" id="totalAdditionals" value="0">
    <input type="hidden" id="totalPassengers" value="<?php echo $totalPassengers ?>">
    <div class="dc-row">
        <div class="dc-col-12 dc-px-0 dc-shadow">
            <?php if($additionals) : ?>
                <div class="dc-row">
                    <div class="dc-col-12 dc-mb-4">
                        <h4 class="elementor-heading-title elementor-size-default dc-underline">
                            <span>
                                Dodatne nadoplate
                            </span>
                        </h4>
                        <?php if($totalPassengers == 1) : ?>
                            <label class="dc-chck-custom-checkbox" for="dc_dodatna_soba">
                                <input type="checkbox" class="dc-chck-checkbox" id="dc_dodatna_soba" data-amount="120" checked="">
                                <span class="dc-chck-checkmark"></span>
                                <span class="dc-chck-label-text">Nadoplata za jednokrevetnu sobu <b>(120.00 €)</b><br><span class="dc-small">Odabrana je doplata za jednokrevetnu sobu jer rezervirate samo za jednog putnika. Ukoliko želite da vam pronađemo smještaj sa nekim drugim, odznačite ovu opciju.</span></span>
                            </label>
                        <?php endif; ?>
                        <?php foreach($additionals as $additional) : ?>
                            <label class="dc-chck-custom-checkbox" for="<?php echo 'i-' . $additional->id; ?>">
                                <input type="checkbox" class="dc-chck-checkbox" id="<?php echo 'i-' . $additional->id; ?>" data-amount="<?php echo $additional->amount; ?>">
                                <span class="dc-chck-checkmark"></span>
                                <span class="dc-chck-label-text"><?php echo $additional->name; ?> <b>(<?php echo $additional->amount ?> €)</b></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="dc-row">
                <div class="dc-col-12">
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Način plaćanja
                        </span>
                    </h4>
                </div>
            </div>
            <div class="dc-row">
                <div class="dc-col-12">
                    <div class="payment-type set_bg_color" data-nacin="3" data-iznos-gotovina="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>" data-iznos-kartice="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>">
                        <div class="payment-title selected">
							<span class="icon dc-icon-nonselected" style="display: none;">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_unchecked_24_regular" fill="#6e6e6e" fill-rule="nonzero">
											<path d="M5.75,3 L18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 Z M5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="icon dc-icon-selected">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_checked_24_regular" fill="#1ca8e1" fill-rule="nonzero">
											<path d="M18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 L18.25,3 Z M18.25,4.5 L5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 Z M10,14.4393398 L16.4696699,7.96966991 C16.7625631,7.6767767 17.2374369,7.6767767 17.5303301,7.96966991 C17.7965966,8.23593648 17.8208027,8.65260016 17.6029482,8.94621165 L17.5303301,9.03033009 L10.5303301,16.0303301 C10.2640635,16.2965966 9.84739984,16.3208027 9.55378835,16.1029482 L9.46966991,16.0303301 L6.46966991,13.0303301 C6.1767767,12.7374369 6.1767767,12.2625631 6.46966991,11.9696699 C6.73593648,11.7034034 7.15260016,11.6791973 7.44621165,11.8970518 L7.53033009,11.9696699 L10,14.4393398 L16.4696699,7.96966991 L10,14.4393398 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="title">PLAĆANJE KARTICAMA ONLINE</span>
                        </div>
                        <div class="payment-desc">
                            <p class="dc-small" style="padding-top: 0; margin-top: 0;">
                                Plaćanje karticom putem našeg webshopa je brzo, jednostavno i potpuno sigurno. Uplata se izvršava odmah, a nakon toga ćete na svoj e-mail dobiti ugovor i potvrdu o rezervaciji putovanja.
                            </p>

                            <div class="dc-row dc_rate_selector" style="padding: 0;">
                                <div class="dc-col-6 dc-mt-3">
                                    <label for="dc_kartica" class="dc-label">Odaberi karticu</label>
                                    <select class="dc-form-control dc-mb-3 dc-select validate" id="dc_kartica" name="dc_kartica">
                                        <option value="VISA">VISA</option>
                                        <option value="MASTERCARD">MASTERCARD</option>
                                        <!--option value="AMEX">AMEX</option-->
                                        <option value="DINERS">DINERS</option>
                                        <option value="MAESTRO">MAESTRO</option>
                                        <option value="MAESTRO (Erste)">MAESTRO (Erste)</option>
                                    </select>
                                </div>
                                <div class="dc-col-6 dc-mt-3">
                                    <label for="dc_rate" class="dc-label">Odaberi broj rata</label>
                                    <select class="dc-form-control dc-mb-3 dc-select validate" id="dc_rate" name="dc_rate">
                                        <option value="Jednokratno">Jednokratno</option>
                                        <?php
                                        for($broj_rata = 2; $broj_rata <= 12; $broj_rata++) {
                                            echo '<option value="' . $broj_rata . '">' . $broj_rata . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-type" data-nacin="1" data-iznos-putovanja="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>" data-za-naplatu="<?php echo number_format($putovanje->akontacija, '2', '.', ''); ?>">
                        <div class="payment-title">
							<span class="icon dc-icon-nonselected">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_unchecked_24_regular" fill="#6e6e6e" fill-rule="nonzero">
											<path d="M5.75,3 L18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 Z M5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="icon dc-icon-selected" style="display: none;">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_checked_24_regular" fill="#1ca8e1" fill-rule="nonzero">
											<path d="M18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 L18.25,3 Z M18.25,4.5 L5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 Z M10,14.4393398 L16.4696699,7.96966991 C16.7625631,7.6767767 17.2374369,7.6767767 17.5303301,7.96966991 C17.7965966,8.23593648 17.8208027,8.65260016 17.6029482,8.94621165 L17.5303301,9.03033009 L10.5303301,16.0303301 C10.2640635,16.2965966 9.84739984,16.3208027 9.55378835,16.1029482 L9.46966991,16.0303301 L6.46966991,13.0303301 C6.1767767,12.7374369 6.1767767,12.2625631 6.46966991,11.9696699 C6.73593648,11.7034034 7.15260016,11.6791973 7.44621165,11.8970518 L7.53033009,11.9696699 L10,14.4393398 L16.4696699,7.96966991 L10,14.4393398 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="title">PRVA RATA KAO POTVRDA, OSTATAK PO ŽELJI</span>
                        </div>
                        <div class="payment-desc dc-small">
                            Omogućujemo uplatu prve rate koja potvrđuje rezervaciju, uz fleksibilnost da preostali iznos uplatite kad Vi želite, do početka putovanja.<br>
                            <br>
                            Preostali iznos potrebno je platiti do polaska putem web stranice na linku  "Online plaćanja" ili uplatom na račun agencije uz poziv na broj rezervacije (koji ćete dobiti na e-mail nakon uplate prve rate).
                        </div>
                    </div>
                    <!--div class="payment-type" data-nacin="2" data-iznos-putovanja="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>" data-za-naplatu="<?php echo number_format($putovanje->akontacija, '2', '.', ''); ?>">
                        <div class="payment-title">
							<span class="icon dc-icon-nonselected">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_unchecked_24_regular" fill="#6e6e6e" fill-rule="nonzero">
											<path d="M5.75,3 L18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 Z M5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="icon dc-icon-selected" style="display: none;">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_checked_24_regular" fill="#1ca8e1" fill-rule="nonzero">
											<path d="M18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 L18.25,3 Z M18.25,4.5 L5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 Z M10,14.4393398 L16.4696699,7.96966991 C16.7625631,7.6767767 17.2374369,7.6767767 17.5303301,7.96966991 C17.7965966,8.23593648 17.8208027,8.65260016 17.6029482,8.94621165 L17.5303301,9.03033009 L10.5303301,16.0303301 C10.2640635,16.2965966 9.84739984,16.3208027 9.55378835,16.1029482 L9.46966991,16.0303301 L6.46966991,13.0303301 C6.1767767,12.7374369 6.1767767,12.2625631 6.46966991,11.9696699 C6.73593648,11.7034034 7.15260016,11.6791973 7.44621165,11.8970518 L7.53033009,11.9696699 L10,14.4393398 L16.4696699,7.96966991 L10,14.4393398 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="title">REZERVACIJA – uplatom na račun agencije</span>
                        </div>
                        <div class="payment-desc dc-small">
                            Na slijedećem koraku biti će Vam prikazane informacije
                            za plaćanje rezervacije u iznosu od <?php echo $putovanje->akontacija; ?> €,
                            općom uplatnicom ili internet bankarstvom. <br>
                            Uplatu preostalog iznosa potrebno je podmiriti do polaska na putovanje putem web stranice koristeći
                            opciju "Online plaćanja" ili uplatom na račun agencije uz poziv na broj narudžbe.
                        </div>
                    </div-->
                    <div class="payment-type" data-nacin="4" data-iznos-putovanja="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>" data-za-naplatu="<?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?>">
                        <div class="payment-title">
							<span class="icon dc-icon-nonselected">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_unchecked_24_regular" fill="#6e6e6e" fill-rule="nonzero">
											<path d="M5.75,3 L18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 Z M5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 L5.75,4.5 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="icon dc-icon-selected" style="display: none;">
								<svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<g id="ic_fluent_checkbox_checked_24_regular" fill="#1ca8e1" fill-rule="nonzero">
											<path d="M18.25,3 C19.7687831,3 21,4.23121694 21,5.75 L21,18.25 C21,19.7687831 19.7687831,21 18.25,21 L5.75,21 C4.23121694,21 3,19.7687831 3,18.25 L3,5.75 C3,4.23121694 4.23121694,3 5.75,3 L18.25,3 Z M18.25,4.5 L5.75,4.5 C5.05964406,4.5 4.5,5.05964406 4.5,5.75 L4.5,18.25 C4.5,18.9403559 5.05964406,19.5 5.75,19.5 L18.25,19.5 C18.9403559,19.5 19.5,18.9403559 19.5,18.25 L19.5,5.75 C19.5,5.05964406 18.9403559,4.5 18.25,4.5 Z M10,14.4393398 L16.4696699,7.96966991 C16.7625631,7.6767767 17.2374369,7.6767767 17.5303301,7.96966991 C17.7965966,8.23593648 17.8208027,8.65260016 17.6029482,8.94621165 L17.5303301,9.03033009 L10.5303301,16.0303301 C10.2640635,16.2965966 9.84739984,16.3208027 9.55378835,16.1029482 L9.46966991,16.0303301 L6.46966991,13.0303301 C6.1767767,12.7374369 6.1767767,12.2625631 6.46966991,11.9696699 C6.73593648,11.7034034 7.15260016,11.6791973 7.44621165,11.8970518 L7.53033009,11.9696699 L10,14.4393398 L16.4696699,7.96966991 L10,14.4393398 Z" id="🎨Color"></path>
										</g>
									</g>
								</svg>
							</span>
                            <span class="title">PLAĆANJE PO PONUDI ILI OSOBNI DOLAZAK U POSLOVNICU</span>
                        </div>
                        <div class="payment-desc dc-small">
                            Plaćanje se vrši gotovinom ili karticama (jednokratno ili na rate), osobnim dolaskom u agenciju.<br>
                            Plaćanje možete izvršiti i putem svog internet bankarstva ili općom uplatnicom na osnovu ponude u potvrdi ove rezervacije.
                        </div>
                    </div>
                </div>
            </div>
            <div class="dc-row dc-my-4">
                <div class="dc-col-12">
                    <div class="dc-summary">
                        <table style="width: 100%; line-height: 1.5;">
                            <tbody>
                            <tr>
                                <td class="dc-end">Iznos putovanja</td>
                                <td class="dc-end total"><span class="dc_iznos_putovanja"><?php echo number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?> €</span></td>
                            </tr>
                            <?php if($additionals) : ?>
                                <tr>
                                    <td class="dc-end">Iznos odabranih dodataka</td>
                                    <td class="dc-end total"><span class="dc_iznos_dodataka"><?php echo $totalPassengers == 1 ? '+ 120.00 €' : '+ 0.00 €'; ?></span></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="dc-end">Sveukupno</td>
                                <td class="dc-end total"><span class="dc_iznos_sveukupno"><?php echo $totalPassengers == 1 ? number_format(($putovanje->ukupni_iznos * $totalPassengers) + 120, '2', '.', '') : number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?> €</span></td>
                            </tr>
                            <tr>
                                <th class="dc-end"><strong>ZA NAPLATU</strong></th>
                                <th class="dc-end total_za_naplatu"><strong><span class="dc_za_naplatu"><?php echo $totalPassengers == 1 ? number_format(($putovanje->ukupni_iznos * $totalPassengers) + 120, '2', '.', '') : number_format($putovanje->ukupni_iznos * $totalPassengers, '2', '.', ''); ?> €</span></strong></th>
                            </tr>
                            <tr>
                                <td colspan="2" class="dc-end dc-small"><br>* Prikazane cijene se odnose za <?php echo $totalPassengers; ?> putnika.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="dc-col-12">
                <div class="dc-scrollbox">
                    <?php echo $settings->dc_postavke->opci_uvjeti ? nl2br($settings->dc_postavke->opci_uvjeti) : ''; ?>
                </div>
            </div>
            <div class="dc-row dc-my-4 dc-mt-3">
                <div class="dc-col-12 dc-mt-3">
                    <label class="dc-chck-custom-checkbox" for="privola_1">
                        <input type="checkbox" class="validate" id="privola_1">
                        <span class="dc-chck-checkmark"></span>
                        <span class="dc-chck-label-text">Dajem privolu na obradu osobnih podataka u svrhu izvršenja usluge koju naručujem. *</span>
                    </label>
                </div>
                <div class="dc-col-12">
                    <label class="dc-chck-custom-checkbox" for="privola_3">
                        <input type="checkbox" class="validate" id="privola_3">
                        <span class="dc-chck-checkmark"></span>
                        <span class="dc-chck-label-text">Pročitao/la sam i slažem se sa <a href="https://eridan.hr/opci-uvjeti/" target="_blank">općim uvjetima korištenja</a> web stranice eridan.hr. *</span>
                    </label>
                </div>
                <div class="dc-col-12">
                    <label class="dc-chck-custom-checkbox" for="privola_2">
                        <input type="checkbox" class="validate" id="privola_2">
                        <span class="dc-chck-checkmark"></span>
                        <span class="dc-chck-label-text">Upoznat sam sa standardnim obrascem koji u skladu s europskom direktivom odabrano putovanje svrstava u turistički paket.</span>
                    </label>
                </div>
                <div class="dc-col-12 dc-mb-3">
                    <label class="dc-chck-custom-checkbox" for="privola_4">
                        <input type="checkbox" class="validate" id="privola_4">
                        <span class="dc-chck-checkmark"></span>
                        <span class="dc-chck-label-text">Upoznat sam s Općim uvjetima i s njima se slažem u ime svih putnika.</span>
                    </label>
                </div>
            </div>
            <div class="dc-row mandatory" style="display: none;">
                <div class="dc-col-12">
                    <p class="dc-alert dc-alert-danger">Polja označena crvenom bojom su obavezna. Molimo da ih popunite.</p>
                </div>
            </div>
            <div class="dc-row mandatory_scroll" style="display: none;">
                <div class="dc-col-12">
                    <p class="dc-alert dc-alert-danger">Za nastavak morate pročitati opće uvjete putovanja do kraja.</p>
                </div>
            </div>
            <div class="dc-row mt-4">
                <div class="dc-col-5 dc-mb-3">
                    <a href="<?php echo get_permalink(); ?>?act=odustani" class="dc-button dc-button-warning" id="odustani_btn">Odustani</a>
                </div>
                <div class="dc-col-7">
                    <!--a class="dc-button dc-button-info" id="reservation_button_disabled" style="width: 100%;"-->
                    <a class="dc-button dc-button-info" id="reservation_button" style="width: 100%;">
                        REZERVIRAJ
                    </a>
                </div>
            </div>
            <?php /* echo '<pre>'; print_r($putovanje); echo '</pre>';*/ ?>
        </div>
    </div>
</form>
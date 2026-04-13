window.dataLayer = window.dataLayer || [];

/**
 * Multi-step quote / order flow (Sterkado API + email handoff).
 * Called from app.js when `.request-quote` exists on the page.
 */
export function initRequestQuote() {
    const $ = window.jQuery;
    if (!$ || !$('.request-quote').length) {
        return;
    }

    formControl();
    stepOneValidation();
    stepTwoValidation();
    accordionToggle();
    infoModal();
    ctaTriggers();
    initFlowStepTracking();
}

const ctaTriggers = () => {
    $('.trigger-quote-cta, .trigger-order-cta').off('click').on('click', function (e) {
        e.preventDefault();

        let ctaType = 'Offerte';

        if ($(this).hasClass('trigger-order-cta')) {
            ctaType = 'Bestelling';
        }

        const state = window.completeSelectionState || {};

        if (!state || Object.keys(state).length === 0) {
            console.warn('No selection state present.');
            return;
        }

        $.ajax({
            url: window.RequestQuoteAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'send_quote_email',
                _ajax_nonce: window.RequestQuoteAjax.nonce,
                state: JSON.stringify(state),
                cta_type: ctaType
            },
        })
            .done((res) => {
                if (res?.success) {
                    // console.log("State :",window.completeSelectionState)
                    try {
                      pushQuoteOrderDataLayer(ctaType, state, res);
                    } catch (err) {
                      console.error('dataLayer push failed:', err);
                    }

                    // 2) Redirect (tiny delay to ensure the push is recorded)
                    const url = (ctaType === 'Offerte') ? window.RequestQuoteAjax.redirectQuote : window.RequestQuoteAjax.redirectOrder;
                    setTimeout(() => { window.location.href = url; }, 60);
                    
                } else {
                    console.error(res?.data || 'Unknown error');
                }
            })
            .fail((xhr) => {
                console.error('AJAX failed', xhr?.responseText);
            })
    });
};

function sanitizeHtml(input) {
    const tpl = document.createElement('template');
    tpl.innerHTML = input || '';

    // Remove dangerous elements
    tpl.content.querySelectorAll('script, style, iframe, object, embed, link, meta').forEach(el => el.remove());

    // Strip event handlers and javascript: URLs
    tpl.content.querySelectorAll('*').forEach(el => {
        [...el.attributes].forEach(attr => {
            if (/^on/i.test(attr.name)) el.removeAttribute(attr.name);
            if ((attr.name === 'href' || attr.name === 'src') && /^\s*javascript:/i.test(attr.value)) {
                el.removeAttribute(attr.name);
            }
        });
    });

    return tpl.innerHTML;
}

let infoModal = function () {
    $('.request-quote').on('click', '.modal-trigger', function () {
        // read the attribute; it was set via JSON.stringify(...)
        const raw = $(this).attr('data-modal-content');

        // tolerate legacy plain strings
        let html;
        try {
            html = JSON.parse(raw);
        } catch (_) {
            html = raw;
        }

        // sanitize, then inject as HTML
        const safeHtml = sanitizeHtml(html);
        $('.request-quote__modal .request-quote__modal-body').html(safeHtml);

        $('.request-quote__modal').addClass('is-open');
    });

    $('.request-quote__modal [data-rq-modal-close], .request-quote__modal [data-rq-modal-overlay]').on('click', function () {
        $('.request-quote__modal').removeClass('is-open');
        setTimeout(function () {
            $('.request-quote__modal .request-quote__modal-body').empty();
        }, 300);
    });
};

let accordionToggle = function () {
    $('.request-quote .request-quote__accordion').on('click', function () {
        $(this).toggleClass('open');
    })
}

let formControl = function () {
    // Buttons inside the step content
    $('.request-quote .go-to-step').click(function () {
        let stepTarget = parseInt($(this).attr('data-step-target'), 10);
        if (!stepTarget) return;
        goToStep(stepTarget);
    });

    // Control navigation at top
    $('.request-quote .controls .step').click(function () {
        let stepTarget = parseInt($(this).attr('data-step-target'), 10);
        if (!stepTarget) return;

        if ($(this).hasClass('completed') || $(this).hasClass('current')) {
            goToStep(stepTarget);
        }
    });

    // 'Edit' link inside a step
    $('.request-quote .request-quote__flow .step .edit').click(function () {
        let $step = $(this).closest('.step');
        let classList = $step.attr('class').split(/\s+/);
        let stepTarget = null;

        classList.forEach(cls => {
            if (cls.startsWith('step-') && !isNaN(cls.split('-')[1])) {
                stepTarget = parseInt(cls.split('-')[1], 10);
            }
        });

        if (stepTarget && ($step.hasClass('completed') || $step.hasClass('current'))) {
            goToStep(stepTarget);
        }
    });

    // Step navigation logic with scroll
    function goToStep(stepTarget) {
        $('.request-quote .sidebar .card').removeClass('hidden');

        $('.request-quote .order-cta').addClass('disabled');

        $('.request-quote .controls .step').each(function (index) {
            let stepNum = index + 1;
            $(this).removeClass('current completed');

            if (stepNum < stepTarget) {
                $(this).addClass('completed');
            } else if (stepNum === stepTarget) {
                $(this).addClass('current');
            }
        });

        $('.request-quote .request-quote__flow .step').each(function (index) {
            let stepNum = index + 1;
            $(this).removeClass('current completed disabled');

            if (stepNum < stepTarget) {
                $(this).addClass('completed');
            } else if (stepNum === stepTarget) {
                $(this).addClass('current');
            } else {
                $(this).addClass('disabled');
            }
        });

        if (stepTarget === 2) {
            // Retrieve shipping costs
            let shippingApiQuantity = parseInt(window.completeSelectionState.employeeAmount);
            let shippingApiLocations = parseInt(window.completeSelectionState.shippingLocations);
            let shippingApiProductId = parseInt(retrievedData.products?.[window.completeSelectionState.productKey].id);

            getShippingCostsData(shippingApiQuantity, shippingApiLocations, shippingApiProductId, function (data) {
                retrievedCosts = data;
            })
        }

        if (stepTarget === 3) {
            // Overview summary
            const overviewHtml = $('.request-quote .overview-wrapper').html();
            $('.request-quote .final-overview-wrapper').html(overviewHtml);

            $('.request-quote .overview-wrapper').parents('.card').addClass('hidden');

            // Data summary
            let userDataHtml = `
                <h4>Gegevens</h4>
                <span class="company">${window.completeSelectionState.userData.companyName}</span>
                <span class="name">${window.completeSelectionState.userData.firstName} ${window.completeSelectionState.userData.lastName}</span>
                <span class="email">${window.completeSelectionState.userData.email}</span>
                <span class="phone">${window.completeSelectionState.userData.phone}</span>
            `;

            if (window.completeSelectionState.userData.notes && window.completeSelectionState.userData.notes.length > 0) {
                userDataHtml += `<span class="notes"><strong>Opmerking:</strong> ${window.completeSelectionState.userData.notes}</span>`;
            }

            $('.request-quote .data-summary').empty().append(userDataHtml);

            // Basic values
            let amount = Number(window.completeSelectionState.employeeAmount) || 0;
            let productPrice = Number(window.completeSelectionState.productPrice) || 0;
            let productVat = Number(window.completeSelectionState.productVat) || 0;
            let extraPrice = Number(window.completeSelectionState.extraPrice) || 0;
            let extraVat = Number(window.completeSelectionState.extraVat) || 0;

            // Override price selection, since for now we'll just use the maximum budget and 21% VAT
            productPrice = Number(window.completeSelectionState.employeeBudget) || 0;
            productVat = 0; // percentage

            // Shipping values
            let shippingLocations = Number(window.completeSelectionState.shippingLocations) || 0;
            let shippingPerLocation = Number(retrievedCosts.shipping.price_per_location) || 0;
            let shippingTotal = Number(retrievedCosts.shipping.price_total) || 0;
            let shippingVat = 0; // percentage

            // Line prices
            let productTotal = amount * productPrice;
            let extraTotal = amount * extraPrice;

            // VAT calculations
            let productVatAmount = (productTotal * productVat) / 100;
            let extraVatAmount = (extraTotal * extraVat) / 100;
            let shippingVatAmount = (shippingTotal * shippingVat) / 100;

            if (typeof retrievedCosts.shipping.price_total === 'string') {
                shippingVatAmount = 0;
                shippingPerLocation = 0;
                shippingTotal = 0;
                shippingVatAmount = 0;
            }

            // Totals
            let totalExclVat = productTotal + extraTotal + shippingTotal;
            let totalInclVat = totalExclVat + productVatAmount + extraVatAmount + shippingVatAmount;

            // HTML output

            let totalsHtml = `
                <h4>Jouw selectie</h4>
                <table>
                <tr>
                    <th>Omschrijving</th>
                    <th>BTW</th>
                    <th>Prijs</th>
                </tr>
                
                <tr class="product">
                    <td>${window.completeSelectionState.productName} x ${amount}</td>
                    <td>0%</td>
                    <td>€ ${productTotal.toFixed(2)}</td>
                </tr>
            `;

            if (window.completeSelectionState.extraName && window.completeSelectionState.extraName.length > 0) {
                totalsHtml += `
                    <tr class="extra">
                        <td>${window.completeSelectionState.extraName} x ${amount}</td>
                        <td>0%</td>
                        <td>€ ${extraTotal.toFixed(2)}</td>
                    </tr>`;
            }

            // totalsHtml += `
            //     <div class="shipping-per-location">
            //         <span>Verzending x ${shippingLocations}</span>
            //         <span>€ ${shippingPerLocation.toFixed(2)}</span>
            //     </div>`;

            if (typeof retrievedCosts.shipping.price_total === 'string') {
                totalsHtml += `
                <tr class="shipping-total">
                    <td>Verzending</td>
                    <td>0%</td>
                    <td class="small-text">${retrievedCosts.shipping.price_total}</td>
                </tr>`;
            } else {
                totalsHtml += `
                <tr class="shipping-total">
                    <td>Verzending</td>
                    <td>0%</td>
                    <td>€ ${shippingTotal.toFixed(2)}</td>
                </tr>`;
            }

            totalsHtml += `
                <tr class="total-excl-vat">
                    <th>Totaal excl. BTW</th> 
                    <td></td>
                    <td>€ ${totalExclVat.toFixed(2)}</td>
                </tr>
            `;

            totalsHtml += `
                <tr class="total-incl-vat">
                    <th>Totaal incl. BTW</th> 
                    <td></td>
                    <td>€ ${totalInclVat.toFixed(2)}</td>
                </tr>
            `;
            $('.request-quote .totals').empty().append(totalsHtml);
            $('.request-quote .order-cta').removeClass('disabled');

            // Set cookies with information to be used in forms
            if (window.completeSelectionState.employeeAmount) {
                setCookie('employee_amount', window.completeSelectionState.employeeAmount, 7);
            }

            if (window.completeSelectionState.employeeBudget) {
                setCookie('employee_budget', window.completeSelectionState.employeeBudget, 7);
            }

            if (window.completeSelectionState.productKey) {
                setCookie('product_key', window.completeSelectionState.productKey, 7);
            }

            if (window.completeSelectionState.variantKey) {
                setCookie('variant_key', window.completeSelectionState.variantKey, 7);
            }

            if (window.completeSelectionState.extraKey) {
                setCookie('extra_key', window.completeSelectionState.extraKey, 7);
            }

            if (window.completeSelectionState.shipping) {
                setCookie('shipping_method', window.completeSelectionState.shipping, 7);
            }

            if (window.completeSelectionState.shippingLocations) {
                setCookie('shipping_locations', window.completeSelectionState.shippingLocations, 7);
            }

            if (window.completeSelectionState.userData.companyName) {
                setCookie('user_company_name', window.completeSelectionState.userData.companyName, 7);
            }

            if (window.completeSelectionState.userData.email) {
                setCookie('user_email', window.completeSelectionState.userData.email, 7);
            }

            if (window.completeSelectionState.userData.firstName) {
                setCookie('user_first_name', window.completeSelectionState.userData.firstName, 7);
            }

            if (window.completeSelectionState.userData.lastName) {
                setCookie('user_last_name', window.completeSelectionState.userData.lastName, 7);
            }

            if (window.completeSelectionState.userData.phone) {
                setCookie('user_phone', window.completeSelectionState.userData.phone, 7);
            }

            if (window.completeSelectionState.userData.notes) {
                setCookie('user_notes', window.completeSelectionState.userData.notes, 7);
            }
        }


        // Smooth scroll to the step
        const $targetStep = $(`.request-quote .request-quote__flow`);
        if ($targetStep.length) {
            $('html, body').animate({
                scrollTop: $targetStep.offset().top - 60
            }, 600);
        }
    }

};

let retrievedData = {};
let retrievedCosts = {};

let stepOneValidation = function () {
    const optionsWrapper = $('.request-quote .step-1 .options-wrapper');
    const overviewWrapper = $('.request-quote .overview-wrapper');
    const rqAjax = window.RequestQuoteAjax || {};
    const placeholderImg = rqAjax.placeholderImage || '';
    const $button = $('.request-quote .go-to-details');

    // Preserve existing user data if any
    const preservedUserData = window.completeSelectionState?.userData || {
        firstName: '', lastName: '', companyName: '', phone: '', email: '', notes: ''
    };

    // Reset selections but keep userData
    window.completeSelectionState = {
        employeeAmount: null,
        employeeBudget: null,
        productName: null,
        productKey: null,
        productPrice: null,
        productVat: null,
        variantName: null,
        variantKey: null,
        variantPrice: null,
        variantVat: null,
        extraName: null,
        extraKey: null,
        extraPrice: null,
        extraVat: null,
        shipping: null,
        shippingLocations: null,
        userData: preservedUserData
    };

    // Enable or disable the "Naar gegevens" button based on current form state
    function validateForm() {
        const employeeAmount = $('.request-quote input[name=employee-amount]').val();
        const employeeBudget = $('.request-quote input[name=employee-budget]').val();
        const product = retrievedData.products?.[window.completeSelectionState.productKey];
        const $button = $('.request-quote .go-to-details');

        // Base checks: need amount, budget, and a product
        if (!employeeAmount || !employeeBudget || !product) {
            $('.request-quote .missing-values-message').addClass('show');
            $button.prop('disabled', true);
            return;
        }

        // If product has variants, one must be chosen
        const hasVariants = product.product_variants && Object.keys(product.product_variants).length > 0;
        if (hasVariants && !window.completeSelectionState.variantKey) {
            $('.request-quote .missing-values-message').addClass('show');
            $button.prop('disabled', true);
            return;
        }

        // Shipping must be chosen
        const hasShipping = product.shipping_methods && Object.keys(product.shipping_methods).length > 0;
        if (!hasShipping || !window.completeSelectionState.shipping) {
            $('.request-quote .missing-values-message').addClass('show');
            $button.prop('disabled', true);
            return;
        }

        // If 'locaties' is chosen, require that the input has *some* value
        if (window.completeSelectionState.shipping === 'locaties') {
            const raw = $('.shipping-locations-group input[name="locations-amount"]').val();
            const hasSomeValue = raw != null && String(raw).trim() !== '';

            if (!hasSomeValue) {
                $('.request-quote .missing-values-message').addClass('show');
                $button.prop('disabled', true);
                return;
            }

            // Store whatever was typed (you can parse/validate later where needed)
            window.completeSelectionState.shippingLocations = raw;
        }

        // All good
        $('.request-quote .missing-values-message').removeClass('show');
        $button.prop('disabled', false);
    }


    // Render current selection summary
    function rebuildOverview() {
        overviewWrapper.empty();

        const employeeAmount = $('.request-quote input[name=employee-amount]').val();
        const employeeBudget = $('.request-quote input[name=employee-budget]').val();
        const product = retrievedData.products?.[window.completeSelectionState.productKey];


        if (employeeAmount) {
            window.completeSelectionState.employeeAmount = employeeAmount;
            overviewWrapper.append(`<div class="employee-amount"><h4>Medewerkers</h4><p>${employeeAmount}</p></div>`);
        }

        if (employeeBudget) {
            window.completeSelectionState.employeeBudget = employeeBudget;
            overviewWrapper.append(`<div class="employee-budget"><h4>Budget</h4><p>€${employeeBudget}</p></div>`);
        }

        if (product) {
            overviewWrapper.append(`<div class="product"><h4>Soort</h4><p>${product.name}</p></div>`);
        }

        const variant = product?.product_variants?.[window.completeSelectionState.variantKey];
        if (variant) {
            overviewWrapper.append(`<div class="variant"><h4>Ontwerp</h4><p>${variant.name}</p></div>`);
        }

        const extra = product?.give_aways?.[window.completeSelectionState.extraKey];
        if (extra) {
            overviewWrapper.append(`<div class="extras"><h4>Extra</h4><p>${employeeAmount}x ${extra.name}</p></div>`);
        }

        const shippingLabel = product?.shipping_methods?.[window.completeSelectionState.shipping];
        const shippingLocations = window.completeSelectionState.shippingLocations;

        if (shippingLabel) {
            if (shippingLocations && shippingLocations > 0) {
                overviewWrapper.append(`<div class="shipping-method"><h4>Verzending</h4><p>${shippingLabel} x${shippingLocations}</p></div>`);
            } else {
                overviewWrapper.append(`<div class="shipping-method"><h4>Verzending</h4><p>${shippingLabel}</p></div>`);
            }
        }
    }

    // Simple debounce helper
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Watch input changes for employee fields
    $('.request-quote input[name=employee-amount], .request-quote input[name=employee-budget]')
        .off('input')
        .on('input', debounce(function () {
            const employeeAmount = $('.request-quote input[name=employee-amount]').val();
            const employeeBudget = $('.request-quote input[name=employee-budget]').val();

            $('.shipping-locations-group').addClass('hidden');
            $('.shipping-locations-group input').val('');

            if (employeeAmount && employeeBudget) {
                const quantity = parseInt(employeeAmount, 10);
                const budget = parseFloat(employeeBudget.replace(',', '.'));

                loadProductData(quantity, budget, function (data) {
                    retrievedData = data;

                    $('.request-quote .no-results').removeClass('show');

                    optionsWrapper.find('.products-group, .variations-group, .extras-group, .shipping-methods-group').remove();

                    if (
                        retrievedData.products &&
                        (
                            (Array.isArray(retrievedData.products) && retrievedData.products.length > 0) ||
                            (!Array.isArray(retrievedData.products) && Object.keys(retrievedData.products).length > 0)
                        )
                    ) {
                        const productsHtml = Object.entries(retrievedData.products).map(([productKey, product]) => {
                            return `
                                <div data-product-key="${productKey}" class="single-product">
                                    <span class="image">
                                        <img src="${product.image || placeholderImg}" alt="${product.name}">
                                    </span>
                                    <div class="product-content">
                                        <h5>
                                            ${product.name}
                                            ${product.info_text ? `<span class="modal-trigger" data-modal-content='${JSON.stringify(product.info_text)}'></span>` : ''}
                                        </h5>
                                        <p>${product.description || ''}</p>
                                    </div>
                                </div>
                            `;
                        }).join('');

                        const productsGroup = `
                        <div class="info-group products-group">
                            <h4>2. Kies een verpakking of e-voucher</h4>
                            <p>De verpakking is kosteloos en later naar wens te personaliseren.</p>
                            <div class="products-wrapper">${productsHtml}</div>
                        </div>`;

                        optionsWrapper.append(productsGroup);

                        // Preserve existing user data if any
                        const preservedUserData = window.completeSelectionState?.userData || {
                            firstName: '', lastName: '', companyName: '', phone: '', email: '', notes: ''
                        };

                        // Reset selections but keep userData
                        window.completeSelectionState = {
                            employeeAmount: null,
                            employeeBudget: null,
                            productName: null,
                            productKey: null,
                            productPrice: null,
                            productVat: null,
                            variantName: null,
                            variantKey: null,
                            variantPrice: null,
                            variantVat: null,
                            extraName: null,
                            extraKey: null,
                            extraPrice: null,
                            extraVat: null,
                            shipping: null,
                            shippingLocations: null,
                            userData: preservedUserData
                        };

                        rebuildOverview();
                        validateForm();
                    } else {
                        $('.request-quote .no-results').addClass('show');
                    }
                });
            }
        }, 250));

    $('.request-quote input[name=employee-budget]').on('blur', function () {
        const val = $(this).val();
        $(this).val(val.replace(',', '.'));
    });

    // Product selection
    $('.request-quote .step-1 .step-content')
        .off('click', '.single-product')
        .on('click', '.single-product', function () {
            const productKey = $(this).data('product-key');
            const product = retrievedData.products?.[productKey];
            if (!product) return;

            let productName = retrievedData.products?.[productKey].name;
            let productPrice = retrievedData.products?.[productKey].price;
            let productVat = retrievedData.products?.[productKey].vat;

            $('.request-quote .step-1 .step-content .selected').removeClass('selected');
            $(this).addClass('selected');

            // Preserve existing user data if any
            const preservedUserData = window.completeSelectionState?.userData || {
                firstName: '', lastName: '', companyName: '', phone: '', email: '', notes: ''
            };

            window.completeSelectionState = {
                productName: productName,
                productKey: productKey,
                productPrice: productPrice,
                productVat: productVat,
                variantName: null,
                variantKey: null,
                variantPrice: null,
                variantVat: null,
                extraName: null,
                extraKey: null,
                extraPrice: null,
                extraVat: null,
                shipping: null,
                shippingLocations: null,
                userData: preservedUserData
            };

            optionsWrapper.find('.variations-group, .extras-group, .shipping-methods-group, .shipping-locations-group').remove();

            let stepCounter = 3;

            if (product.product_variants) {
                const variantsHtml = Object.entries(product.product_variants).map(([variantKey, variant]) => `
                    <div class="single-variant" data-variant-key="${variantKey}">
                        <span class="image">
                            <img src="${variant.image || placeholderImg}" alt="${variant.name}">
                        </span>
                        <div class="product-content">
                            <h5>${variant.name}</h5>
                            <p>${variant.description || ''}</p>
                        </div>
                    </div>
                `).join('');

                optionsWrapper.append(`
                    <div class="info-group variations-group">
                        <h4>${stepCounter++}. Kies een ontwerp</h4>
                        <p>Kies voor de standaard opties of maak later een eigen ontwerp.</p>
                        <div class="variants-wrapper">${variantsHtml}</div>
                    </div>
                `);
            }

            if (product.give_aways) {
                const extrasHtml = Object.entries(product.give_aways).map(([extraKey, extra]) => `
                    <div class="single-extra" data-extra-key="${extraKey}">
                        <span class="image">
                            <img src="${extra.image || placeholderImg}" alt="${extra.name}">
                        </span>
                        <div class="extra-content">
                            <h5>
                                ${extra.name}
                                ${extra.info_text ? `<span class="modal-trigger" data-modal-content='${JSON.stringify(extra.info_text)}'></span>` : ''}
                            </h5>
                            <p class="price">+ € ${extra.price || '0.00'} p.p.</p>
                        </div>
                    </div>
                `).join('');

                optionsWrapper.append(`
                    <div class="info-group extras-group">
                        <h4>${stepCounter++}. Leuk extraatje toevoegen?</h4>
                        <p>Geef je cadeau een extra touch met een van deze extra’s.</p>
                        <div class="extras-wrapper">${extrasHtml}</div>
                    </div>
                `);
            }

            if (product.shipping_methods) {
                const SHIPPING_IMAGES = rqAjax.shippingImages || {};

                const shippingHtml = Object.entries(product?.shipping_methods ?? {}).map(([key, label]) => {
                    const shippingImage = SHIPPING_IMAGES[key] ?? placeholderImg;
                    let shippingDescription = '';

                    if(key === 'locatie') {
                        shippingDescription = 'Alle cadeaus worden in één keer naar hetzelfde adres geleverd.';
                    }

                    if(key === 'locaties') {
                        shippingDescription = 'Levering naar verschillende vestigingen of locaties tegelijk.';
                    }

                    if(key === 'huisadressen') {
                        shippingDescription = 'Cadeaus rechtstreeks naar de ontvangers thuis gestuurd.';
                    }

                    if(key === 'email') {
                        shippingDescription = 'Medewerkers ontvangen hun persoonlijke webshop link direct in de mailbox.';
                    }


                    return `
                        <div class="single-shipping-method" data-method="${key}">
                          <span class="image"><img src="${shippingImage}" alt="Verzending"></span>
                          <div class="shipping-method-content">
                            <h5>${label}</h5>
                            <p>${shippingDescription}</p>
                          </div>
                        </div>
                      `;
                }).join('');

                optionsWrapper.append(`
                    <div class="info-group shipping-methods-group">
                        <h4>${stepCounter++}. Verzendmethode</h4>
                        <p>Kies hoe je cadeau wordt bezorgd: rechtstreeks naar de ontvanger of naar één of meerdere locaties.</p>
                        <div class="shipping-methods-wrapper">${shippingHtml}</div>
                    </div>
                `);

                optionsWrapper.append(`
                    <div class="info-group shipping-locations-group hidden">
                        <h4>${stepCounter++}. Aantal locaties</h4>
                        <p>Om hoeveel vestigingen of locaties gaat het?</p>
                        <div class="locations-selection form-group">
                            <div class="input-group">
                                <input type="number" step="1" min="2" name="locations-amount">
                            </div>
                        </div>
                    </div>
                `);
            }

            rebuildOverview();
            validateForm();
        });

    // Variant selection
    $('.request-quote .step-1 .step-content')
        .off('click', '.single-variant')
        .on('click', '.single-variant', function () {
            const variantKey = $(this).data('variant-key');
            const product = retrievedData.products?.[window.completeSelectionState.productKey];
            const variantName = product?.product_variants?.[variantKey]?.name || '';
            const variantPrice = product?.product_variants?.[variantKey]?.price || '';
            const variantVat = product?.product_variants?.[variantKey]?.vat || '';

            $('.request-quote .step-1 .single-variant').removeClass('selected');
            $(this).addClass('selected');

            window.completeSelectionState.variantName = variantName;
            window.completeSelectionState.variantKey = variantKey;
            window.completeSelectionState.variantPrice = variantPrice;
            window.completeSelectionState.variantVat = variantVat;

            rebuildOverview();
            validateForm();
        });

    // Extra selection (limit to one)
    $('.request-quote .step-1 .step-content')
        .off('click', '.single-extra')
        .on('click', '.single-extra', function () {
            const extraKey = $(this).data('extra-key');
            const product = retrievedData.products?.[window.completeSelectionState.productKey];
            const extraName = product?.give_aways?.[extraKey]?.name || '';
            const extraPrice = product?.give_aways?.[extraKey]?.price || '';
            const extraVat = product?.give_aways?.[extraKey]?.vat || '';

            // Deselect all
            $('.request-quote .step-1 .single-extra').removeClass('selected');

            // Select clicked
            $(this).addClass('selected');

            window.completeSelectionState.extraName = extraName;
            window.completeSelectionState.extraKey = extraKey;
            window.completeSelectionState.extraPrice = extraPrice;
            window.completeSelectionState.extraVat = extraVat;

            rebuildOverview();
            validateForm();
        });

    // Shipping method selection
    $('.request-quote .step-1 .step-content')
        .off('click', '.single-shipping-method')
        .on('click', '.single-shipping-method', function () {
            const methodKey = $(this).data('method');

            $('.request-quote .step-1 .single-shipping-method').removeClass('selected');
            $(this).addClass('selected');

            window.completeSelectionState.shipping = methodKey;

            if (methodKey === 'locaties') {
                $('.shipping-locations-group').removeClass('hidden');
            } else {
                $('.shipping-locations-group').addClass('hidden');
                $('.shipping-locations-group input').val('');

                if (methodKey === 'locatie' || methodKey === 'email') {
                    window.completeSelectionState.shippingLocations = 1;
                }

                if (methodKey === 'huisadressen') {
                    window.completeSelectionState.shippingLocations = parseInt(window.completeSelectionState.employeeAmount);
                }
            }

            rebuildOverview();
            validateForm();
        });

    $('.request-quote .step-1 .step-content')
        .on('change', '.shipping-locations-group input', function () {
            if ($('.shipping-locations-group input').val() > 0) {
                window.completeSelectionState.shippingLocations = parseInt($('.shipping-locations-group input').val());
            } else {
                window.completeSelectionState.shippingLocations = 1;
            }

            rebuildOverview();
            validateForm();
        });

    // Initialize disabled state
    $button.prop('disabled', true);
};

// Add this to your existing script (after stepOneValidation or in its own block)
let stepTwoValidation = function () {
    // Add validation logic
    function validateStep2Form() {
        const $step = $('.request-quote .step-2');
        const $button = $step.find('.go-to-step');

        const firstName = $step.find('input[name="first-name"]').val().trim();
        const lastName = $step.find('input[name="last-name"]').val().trim();
        const companyName = $step.find('input[name="company-name"]').val().trim();
        const phone = $step.find('input[name="phone"]').val().trim();
        const email = $step.find('input[name="email"]').val().trim();
        const notes = $step.find('textarea[name="notes"]').val().trim();

        // Store to global object
        window.completeSelectionState.userData = {
            firstName: firstName, lastName: lastName, companyName: companyName, phone: phone, email: email, notes: notes
        };

        const allValid = firstName && lastName && companyName && phone && email;
        $button.prop('disabled', !allValid);
    }

// Attach listeners to inputs
    $('.request-quote .step-2 input, .request-quote .step-2 textarea').on('input', function () {
        validateStep2Form();
    });

// Initial validation
    validateStep2Form();
};

/**
 * Load products from Sterkado via AJAX.
 *
 * @param {number} quantity - Number of employees.
 * @param {number} budget - Budget per employee.
 * @param {function} callback - Function to execute with the returned product data.
 */
function loadProductData(quantity, budget, callback) {
    if (!Number.isFinite(quantity) || quantity <= 0 || !Number.isFinite(budget) || budget <= 0) {
        console.warn('Invalid quantity or budget passed to loadProductData()', {quantity, budget});
        alert('Voer een geldig aantal medewerkers en budget in.');
        return;
    }

    $.ajax({
        url: (window.RequestQuoteAjax && window.RequestQuoteAjax.ajax_url) || '/wp-admin/admin-ajax.php',
        method: 'GET',
        data: {
            action: 'get_sterkado_products',
            quantity: quantity,
            budget: budget
        },
        success: function (response) {
            if (response?.success && response.data) {
                callback(response.data);
            } else {
                console.error('Sterkado API responded with invalid data:', response);
                alert('Er ging iets mis bij het ophalen van de producten. Probeer het opnieuw of neem contact op.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Sterkado API AJAX error:', error, xhr.responseText);
            alert('Kan geen verbinding maken met de Sterkado API. Probeer het later opnieuw.');
        }
    });
}

/**
 * Get shipping costs for selection via AJAX.
 *
 * @param {number} quantity - Number of employees.
 * @param {number} locations - Amount of selected locations
 * @param {number} productId - Id of the selected product
 * @param {function} callback - Function to execute with the returned shipping costs.
 */
function getShippingCostsData(quantity, locations, productId, callback) {
    if (!Number.isFinite(quantity) || quantity <= 0 || !Number.isFinite(productId) || productId <= 0) {
        console.warn('Invalid quantity, locations or productId passed to getShippingCostsData()', {
            quantity,
            locations,
            productId
        });
        alert('Voer een geldig aantal producten, locaties en een product id in.');
        return;
    }

    $.ajax({
        url: (window.RequestQuoteAjax && window.RequestQuoteAjax.ajax_url) || '/wp-admin/admin-ajax.php',
        method: 'GET',
        data: {
            action: 'get_sterkado_shipping_costs',
            quantity: quantity,
            locations: locations,
            product_id: productId
        },
        success: function (response) {
            if (response?.success && response.data) {
                callback(response.data)
            } else {
                console.error('Sterkado API responded with invalid data:', response);
                alert('Er ging iets mis bij het ophalen van de verzendkosten. Probeer het opnieuw of neem contact op.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Sterkado API AJAX error:', error, xhr.responseText);
            alert('Kan geen verbinding maken met de Sterkado API. Probeer het later opnieuw.');
        }
    });
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}



var initFlowStepTracking = function () {
  var $root = $('.request-quote .request-quote__flow');
  if (!$root.length) return;

  var stepSelector = '.step';

  function currentStepIndex() {
    var $steps = $root.find(stepSelector);
    var $current = $root.find(stepSelector + '.current').first();
    var idx = $steps.index($current);
    return idx >= 0 ? idx + 1 : -1; 
  }

  var prevStep = currentStepIndex();           
  var lastCompletionPushed = null;             

  function pushCompletion(step, reason) {
    if (step > 0 && step !== lastCompletionPushed) {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: 'form_step', step: step });
      console.log('[form_step] completed step', step, 'reason:', reason);
      lastCompletionPushed = step;
    }
  }

  function maybePushCompletion(reason) {
    var newStep = currentStepIndex();
    if (newStep > 0 && newStep !== prevStep) {
      if (prevStep > 0) pushCompletion(prevStep, reason);
      prevStep = newStep;
    }
  }

  var rootEl = $root.get(0);
  if (window.MutationObserver && rootEl) {
    var mo = new MutationObserver(function () {
      setTimeout(function(){ maybePushCompletion('mutation'); }, 0);
    });
    mo.observe(rootEl, {
      attributes: true,
      subtree: true,
      attributeFilter: ['class', 'data-active'],
      childList: true
    });
    $root.data('formStepObserver', mo);
  } else {
    var pollId = setInterval(function(){ maybePushCompletion('poll'); }, 300);
    $root.data('formStepPoll', pollId);
  }

  $root.on('click', '.btn.go-to-step, .btn.go-to-details, .step .edit', function () {
    setTimeout(function(){ maybePushCompletion('nav-click'); }, 50);
  });

  $root.on('click', '.trigger-quote-cta, .trigger-order-cta', function () {
    var cur = currentStepIndex();
    if (cur > 0 && cur !== lastCompletionPushed) {
      pushCompletion(cur, 'cta-click'); // completes the last visible step (e.g., step 3)
    }
  });
};


// Build and push the datalayer payload using ONLY window.completeSelectionState for items.
function pushQuoteOrderDataLayer(ctaType, state, res) {
  var $root = $('.request-quote .request-quote__flow');
  if (!$root.length) { $root = $(document); }

  // --- helpers ---
  var n = (typeof window.num === 'function')
    ? window.num
    : function (x, fb) {
        fb = (typeof fb === 'number') ? fb : 0;
        if (typeof x === 'number') return isFinite(x) ? x : fb;
        var s = (x == null ? '' : String(x)).replace(/[^\d,.\-]/g, '').replace(',', '.');
        var p = parseFloat(s);
        return isFinite(p) ? p : fb;
      };

  function i(x, fb) {
    fb = (typeof fb === 'number') ? fb : 0;
    var s = (x == null ? '' : String(x)).replace(/[^\d\-]/g, '');
    var p = parseInt(s, 10);
    return isFinite(p) ? p : fb;
  }

  function getVal(name) {
    var $el = $root.find('[name="' + name + '"]').first();
    return $.trim($el.val() || '');
  }

  // Build items STRICTLY from selection state
  function buildItemsFromSelection(sel, currency) {
    var items = [];
    var curr  = currency || 'EUR';
    if (!sel || typeof sel !== 'object') return items;

    // First item (required fields)
    var item1_name = $.trim(sel.productName || '');
    var item1_id   = $.trim(sel.productKey   || '');
    var item1_qty  = i(sel.employeeAmount, 0);      // quantity = employeeAmount
    var item1_price= n(sel.employeeBudget, 0);      // price = employeeBudget

    if (item1_name || item1_id) {
      items.push({
        item_id:   String(item1_id),
        item_name: item1_name || String(item1_id),
        currency:  curr,
        price:     item1_price,
        quantity:  item1_qty
      });
    }

    // Second item (optional): only if extraName is non-empty
    var extraName = $.trim(sel.extraName || '');
    if (extraName) {
      var item2_id    = $.trim(sel.extraKey   || '');
      var item2_price = n(sel.extraPrice, 0);
      var item2_qty   = item1_qty; // change to 1 if extras are per-order, not per-employee

      items.push({
        item_id:   String(item2_id),
        item_name: extraName,
        currency:  curr,
        price:     item2_price,
        quantity:  item2_qty
      });
    }

    return items;
  }

  // ---- event name
  var eventName = (ctaType === 'Offerte') ? 'custom_quote_offer' : 'custom_quote_order';

  // ---- userData from inputs (exact keys only)
  var userData = {
    email:      getVal('email'),
    phone:      getVal('phone'),
    first_name: getVal('first-name'),
    last_name:  getVal('last-name')
  };

  // ---- selection state & items
  var currency = 'EUR';
  var sel = state || window.completeSelectionState || {};
  var items = buildItemsFromSelection(sel, currency);

  // ---- NEW SHIPPING & TOTAL LOGIC ----
  // shipping = retrievedCosts.shipping.price_total (or 0 if invalid)
  var shipping = 0;
  if (retrievedCosts && retrievedCosts.shipping) {
    shipping = n(retrievedCosts.shipping.price_total, 0);
  }

  // TOTAL = (employeeAmount * employeeBudget) + shipping
  var empQty   = i(sel.employeeAmount, 0);
  var empPrice = n(sel.employeeBudget, 0);
  var value    = (empQty * empPrice) + shipping;

  // tax fixed to 0
  var tax = 0;

  // ---- push (exact schema only)
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: eventName,
    userData: userData,
    ecommerce: {
      currency: currency,
      value: value,
      tax: tax,
      shipping: shipping,
      items: items
    }
  });
}

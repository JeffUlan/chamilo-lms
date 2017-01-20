<div class="row">
    <div id="message-alert">

    </div>
    <div class="col-md-5 panel panel-default buycourse-panel-default">
        <h3 class="panel-heading">{{ 'PurchaseData'|get_plugin_lang('BuyCoursesPlugin') }}</h3>
        <legend></legend>
        {% if buying_course %}
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xs-12">
                    <a class="ajax" data-title="{{ course.title }}" href="{{ _p.web_ajax ~ 'course_home.ajax.php?' ~ {'a': 'show_course_information', 'code': course.code}|url_encode() }}">
                        <img alt="{{ course.title }}" class="img-responsive" style="width: 100%;" src="{{ course.course_img ? course.course_img : 'session_default.png'|icon() }}">
                    </a>
                </div>
                <div class="col-sm-12 col-md-12 col-xs-12">
                    <h3>
                        <a class="ajax" data-title="{{ course.title }}" href="{{ _p.web_ajax ~ 'course_home.ajax.php?' ~ {'a': 'show_course_information', 'code': course.code}|url_encode() }}">{{ course.title }}</a>
                    </h3>
                    <ul class="list-unstyled">
                        {% for teacher in course.teachers %}
                            <li><em class="fa fa-user"></em> {{ teacher }}</li>
                        {% endfor %}
                    </ul>
                    <p id="n-price" class="lead text-right" style="color: white;"><span class="label label-primary">{{ course.currency == 'BRL' ? 'R$' : course.currency }} {{ course.price }}</span></p>
                    <p id="s-price" class="lead text-right"></p>
                </div>
            </div>
        {% elseif buying_session %}
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xs-12">
                    <p>
                        <img alt="{{ session.name }}" class="img-responsive" style="width: 100%;" src="{{ session.image ? session.image : 'session_default.png'|icon() }}">
                    </p>
                </div>
                <div class="col-sm-12 col-md-12 col-xs-12">
                    <h3>{{ session.name }}</h3>
                    <p><em class="fa fa-calendar fa-fw"></em> {{ session.dates.display }}</p>
                    <ul class="list-unstyled">
                        {% for course in session.courses %}
                            <li>
                                <em class="fa fa-book fa-fw"></em> {{ course.title }}
                                {% if course.coaches|length %}
                                    <ul>
                                        {% for coach in course.coaches %}
                                            <li><em class="fa fa-user fa-fw"></em>{{ coach }}</li>
                                        {% endfor %}
                                    </ul>
                                {% endif %}
                            </li>
                        {% endfor %}
                    </ul>
                    <p id="n-price" class="lead text-right" style="color: white;"><span class="label label-primary">{{ session.currency == 'BRL' ? 'R$' : session.currency }} {{ session.price }}</span></p>
                    <p id="s-price" class="lead text-right"></p>
                </div>
            </div>
        {% elseif buying_service %}
            <div class="col-sm-12 col-md-12 col-xs-12">
                <a href='{{ _p.web }}service/{{ service.id }}'>
                    <img alt="{{ service.name }}" class="img-responsive" src="{{ _p.web }}plugin/buycourses/uploads/services/images/{{ service.image }}">
                </a>
            </div>
            <div class="col-sm-12 col-md-12 col-xs-12">
                <h3>
                    <a href='{{ _p.web }}service/{{ service.id }}'>{{ service.name }}</a>
                </h3>
                <ul class="list-unstyled">
                    {% if service.applies_to == 0 %}
                        <li><em class="fa fa-hand-o-right"></em> {{ 'AppliesTo'|get_plugin_lang('BuyCoursesPlugin') }} {{ 'None' | get_lang }}</li>
                    {% elseif service.applies_to == 1 %}
                        <li><em class="fa fa-hand-o-right"></em> {{ 'AppliesTo'|get_plugin_lang('BuyCoursesPlugin') }} {{ 'User' | get_lang }}</li>
                    {% elseif service.applies_to == 2 %}
                        <li><em class="fa fa-hand-o-right"></em> {{ 'AppliesTo'|get_plugin_lang('BuyCoursesPlugin') }} {{ 'Course' | get_lang }}</li>
                    {% elseif service.applies_to == 3 %}
                        <li><em class="fa fa-hand-o-right"></em> {{ 'AppliesTo'|get_plugin_lang('BuyCoursesPlugin') }} {{ 'Session' | get_lang }}</li>
                    {% endif %}
                    <li><em class="fa fa-money"></em> {{ 'Price'|get_plugin_lang('BuyCoursesPlugin') }} : {{ service.currency == 'BRL' ? 'R$' : service.currency }} {{ service.price }} / {{ service.duration_days == 0 ? 'NoLimit' | get_lang  : service.duration_days ~ ' ' ~ 'Days' | get_lang }} </li>
                    <li><em class="fa fa-user"></em> {{ service.owner_name }}</li>
                    <li><em class="fa fa-align-justify"></em> {{ service.description }}</li>
                </ul>
                <p id="n-price" class="lead text-right" style="color: white;"><span class="label label-primary">{{ service.currency == 'BRL' ? 'R$' : service.currency }} {{ service.price }}</span></p>
                <p id="s-price" class="lead text-right"></p>
            </div>
        {% endif %}
    </div>
    <div class="col-md-1">
    </div>
    <div class="col-md-6 panel panel-default buycourse-panel-default">
        <h3 class="panel-heading">{{ 'TermsAndConditions' | get_plugin_lang('BuyCoursesPlugin') }}</h3>
        <legend></legend>
        <textarea style="width: 100%; height: 345px;" readonly>
            {{ terms ? terms : 'NoTermsAndConditionsProvided' | get_plugin_lang('BuyCoursesPlugin') }}
        </textarea>
        <br />
        <div class="form-group">
            <div class="col-sm-1"><input class="" type="checkbox" id="confirmTermsAndConditons" name="confirmTermsAndConditons" > </div>
            <label class="col-sm-11">{{ 'IConfirmIReadAndAcceptTermsAndCondition'|get_plugin_lang('BuyCoursesPlugin') }}</label>
        </div>
        <br/>
    </div>
</div>

{% if is_bank_transfer %}
    <div class="row">
        <div class="col-xs-12">
            <h3 class="page-header">{{ 'BankAccountInformation'|get_plugin_lang('BuyCoursesPlugin') }}</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ 'Name'|get_lang }}</th>
                            <th class="text-center">{{ 'BankAccount'|get_plugin_lang('BuyCoursesPlugin') }}</th>
                            <th class="text-center">{{ 'SWIFT'|get_plugin_lang('BuyCoursesPlugin') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for account in transfer_accounts %}
                            <tr>
                                <td>{{ account.name }}</td>
                                <td class="text-center">{{ account.account }}</td>
                                <td class="text-center">{{ account.swift }}</td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
            <p>{{ 'OnceItIsConfirmedYouWillReceiveAnEmailWithTheBankInformationAndAnOrderReference'|get_plugin_lang('BuyCoursesPlugin') }}</p>
        </div>
    </div>
{% endif %}

{{ form }}

<script>
    $(document).ready(function () {

        $("#confirm").prop( "disabled", true );

        $("#confirmTermsAndConditons").click(function () {
            if ($("#confirmTermsAndConditons").is(':checked')) {
                $("#confirm").prop( "disabled", false );
            } else {
                $("#confirm").prop( "disabled", true );
            }
        });

        {% if is_culqi_payment %}

            var price = {{ price }} * 100;

            Culqi.codigoComercio = '{{ culqi_params.commerce_code }}';
            Culqi.configurar({
                nombre: '{{ _s.institution }}',
                orden: '{{ sale.reference ?  sale.reference : buying_service.reference }}',
                moneda: '{{ currency.iso_code }}',
                descripcion: '{{ title }}',
                monto: price
            });

            $("#confirm").click(function (e) {
                Culqi.abrir();
                e.preventDefault();
                $(".culqi_checkout").watch('style', function() {

                    if (Culqi.error) {
                        $("#message-alert").html('<div class="col-md-12 alert alert-danger">{{ 'ErrorOccurred'|get_plugin_lang('BuyCoursesPlugin')|format(Culqi.error.codigo, Culqi.error.mensaje) }}</div>')
                    } else if (Culqi.token) {

                        {% if buying_service %}

                            var url = '{{ _p.web_plugin }}buycourses/src/buycourses.ajax.php?a=culqi_cargo_service&token_id=' + Culqi.token.id + '&service_sale_id=' + {{ buying_service.id }};

                        {% else %}

                            var url = '{{ _p.web_plugin }}buycourses/src/buycourses.ajax.php?a=culqi_cargo&token_id=' + Culqi.token.id + '&sale_id=' + {{ sale.id }};

                        {% endif %}

                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {
                                $("#confirm").html('<em class="fa fa-spinner fa-pulse fa-fw" ></em> {{ 'Loading' | get_lang }}');
                                $("#confirm").prop( "disabled", true );
                                $("#cancel").prop( "disabled", true );
                            },
                            success: function() {
                                window.location = "{{ _p.web_plugin }}buycourses/index.php";
                            }
                        })
                    }

                    $(".culqi_checkout").unwatch('style');
                });

                return false;
            });

            $.fn.watch = function(property, callback) {
                return $(this).each(function() {

                    var old_property_val = this[property];
                    var timer;

                    function watch() {
                        var self = $(".culqi_checkout");
                        self = self[0];

                        if ($(self).data(property + '-watch-abort') == true) {
                            timer = clearInterval(timer);
                            $(self).data(property + '-watch-abort', null);
                            return;
                        }
                        if (self[property] != old_property_val) {
                            old_property_val = self[property];
                            callback.call(self);
                        }
                    }
                    timer = setInterval(watch, 700);
                });
            };

            $.fn.unwatch = function(property) {
                return $(this).each(function() {
                    $(this).data(property + '-watch-abort', true);
                });
            };

        {% endif %}
    })
</script>

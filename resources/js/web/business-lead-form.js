import Inputmask from "inputmask";
import axios from "axios";
import { webCustom } from "./global-custom.js";

class BusinessLeadForm {
    constructor() {
        this.form = document.querySelector('#business-lead-form');

        if (!this.form) {
            console.log('Form not found');
            return;
        }

        this.validator;

        this.initForm();
        this.handleFormSubmit();
    }

    initForm() {
        webCustom.initMasks();
        webCustom.googleRecaptcha(this.form);
        webCustom.formRuleCheck(this.form);
    }

    handleFormSubmit() {
        const validationRules = this.getValidationRules();

        this.validator = FormValidation.formValidation(this.form, validationRules);
        this.addCustomValidation();

        const submitButton = this.form.querySelector('[data-form-action="submit"]');
        this.handleSubmitButton(submitButton);
    }

    getValidationRules() {
        return {
            fields: {
                'name': {
                    validators: {
                        notEmpty: {
                            message: 'O nome é obrigatório.'
                        }
                    }
                },
                'email': {
                    validators: {
                        notEmpty: {
                            message: 'O email é obrigatório.'
                        },
                        emailAddress: {
                            message: 'O email precisa ser válido.'
                        }
                    }
                },
                'phone': {
                    validators: {
                        notEmpty: {
                            message: 'O telefone é obrigatório.'
                        }
                    }
                },

                // Custom Fields
                'company': {
                    validators: {
                        notEmpty: {
                            message: 'A empresa é obrigatória.'
                        }
                    }
                },
                'company_segment': {
                    validators: {
                        notEmpty: {
                            message: 'O segmento é obrigatório.'
                        }
                    }
                },
                'company_occupation': {
                    validators: {
                        notEmpty: {
                            message: 'O cargo é obrigatório.'
                        }
                    }
                },
                'company_employees': {
                    validators: {
                        notEmpty: {
                            message: 'O nº de funcionários é obrigatório.'
                        }
                    }
                },
                'company_target': {
                    validators: {
                        notEmpty: {
                            message: 'O público alvo é obrigatório.'
                        }
                    }
                },
                'company_website': {
                    validators: {
                        uri: {
                            message: 'A URL não é valida.',
                        }
                    }
                },
                // End::Custom Fields

                // 'message': {
                //     validators: {
                //         notEmpty: {
                //             message: 'A mensagem é obrigatório.'
                //         }
                //     }
                // }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5()
            }
        };
    }

    addCustomValidation() {
        // Make something, if necessary
    }

    handleSubmitButton(submitButton) {
        submitButton.addEventListener('click', (event) => {
            event.preventDefault();

            if (this.validator) {
                this.validator.validate()
                    .then((status) => {
                        if (status === 'Valid') {
                            submitButton.setAttribute('data-indicator', 'on');
                            webCustom.submitButtonToggleIndicator(submitButton);
                            submitButton.disabled = true;

                            // this.form.submit(); // Submit form

                            this.submitAjaxForm(submitButton);
                        } else {
                            webCustom.showValidatorErrorMessage();
                        }
                    });
            }
        });
    }

    submitAjaxForm(submitButton) {
        axios.post(this.form.getAttribute('action'), new FormData(this.form))
            .then((response) => {
                if (response.data.success) {
                    this.handleSuccessResponse(response.data);
                } else {
                    webCustom.showFormErrorMessage(response.data);
                }
            }).catch((error) => {
                webCustom.showSystemErrorMessage(error);
            }).then(() => {
                submitButton.removeAttribute('data-indicator');
                webCustom.submitButtonToggleIndicator(submitButton);
                submitButton.disabled = false;
            });
    }

    handleSuccessResponse(response) {
        Swal.fire({
            title: "O formulário foi enviado com sucesso!",
            text: response.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, entendi!",
            customClass: {
                confirmButton: "button button-primary button-small button-rounded"
            }
        }).then((result) => {
            if (response.fbq_track) {
                fbq('track', response.fbq_track);
            }

            const redirectRoute = this.form.getAttribute('data-redirect-url');

            if (redirectRoute && redirectRoute !== null) {
                return window.location = redirectRoute;
            }

            webCustom.clearForm(this.form);
            webCustom.googleRecaptcha(this.form);
            webCustom.formRuleCheck(this.form);

            // location.reload(); // reload page

            if (result.isConfirmed) {
                // make something, if necessary
            }
        });
    }
}

const businessLeadForm = new BusinessLeadForm();


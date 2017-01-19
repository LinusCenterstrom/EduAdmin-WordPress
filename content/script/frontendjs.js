var eduBookingView = {
	Customer: null,
	ContactPerson: null,
	Participants: [],
	SingleParticipant: false,
	MaxParticipants: 0,
	CurrentParticipants: 0,
	DiscountPercent: 0,
	AddParticipant: function() {
		if(!eduBookingView.SingleParticipant) {
			if(eduBookingView.MaxParticipants == -1 || eduBookingView.CurrentParticipants < eduBookingView.MaxParticipants)
			{
				var holder = document.getElementById('edu-participantHolder');
				var tmpl = document.querySelector('.eduadmin .participantItem.template');
				var cloned = tmpl.cloneNode(true);
				cloned.style.display = 'block';
				cloned.className = cloned.className.replace(' template', '');
				holder.appendChild(cloned);
			}
			else
			{
				var partWarning = document.getElementById('edu-warning-participants');
				if(partWarning)
				{
					partWarning.style.display = 'block';
					setTimeout(function() {
						var partWarning = document.getElementById('edu-warning-participants');
						partWarning.style.display = '';
					}, 5000);
				}
			}
		}
		eduBookingView.UpdatePrice();
	},
	RemoveParticipant: function(obj) {
		var participantHolder = document.getElementById('edu-participantHolder');
		participantHolder.removeChild(obj.parentNode.parentNode);
		eduBookingView.UpdatePrice();
	},
	SelectEvent: function(obj) {
		var eventid = obj.value;
		if(eventid !== "-1") {
			location.href = '?eid=' + eventid;
		}
	},
	CheckParticipantCount: function()
	{
		var participants = (eduBookingView.SingleParticipant ? 1 : document.querySelectorAll('.eduadmin .participantItem:not(.template):not(.contactPerson)').length - 1);
		if(participants >= eduBookingView.MaxParticipants && eduBookingView.MaxParticipants >= 0) {
			return false;
		}
		return true;
	},
	UpdatePrice: function() {
		var contactParticipant = document.getElementById('contactIsAlsoParticipant');
		var contact = 0;
		if(contactParticipant) {
			if(contactParticipant.checked) {
				contact = 1;
			} else {
				contact = 0;
			}
		}
		eduBookingView.ContactAsParticipant();
		eduBookingView.CurrentParticipants = (eduBookingView.SingleParticipant ? 1 : document.querySelectorAll('.eduadmin .participantItem:not(.template):not(.contactPerson)').length + contact);

		var questions = document.querySelectorAll('.questionPanel [data-price]');

		var questionPrice = 0.0;
		for(var qi = 0; qi < questions.length; qi++) {
			var question = questions[qi];
			var price = parseFloat(question.dataset.price);
			var qtype = question.dataset.type;
			if(!isNaN(price)) {
				switch(qtype) {
					case "number":
						if(question.value != '' && !isNaN(question.value) && parseInt(question.value) > 0) {
							questionPrice += (price * parseInt(question.value));
						} else {
							question.value = '';
						}
						break;
					case "text":
						if(question.value != '') {
							questionPrice += price;
						}
						break;
					case "note":
						if(question.value != '') {
							questionPrice += price;
						}
						break;
					case "radio":
						if(question.checked) {
							questionPrice += price;
						}
						break;
					case "check":
						if(question.checked) {
							questionPrice += price;
						}
						break;
					case "dropdown":
						if(question.selected) {
							questionPrice += price;
						}
						break;
					case "infotext":
						questionPrice += price;
						break;
					case "date":
						if(question.value != '') {
							questionPrice += price;
						}
						break;
				}
			}
		}

		var priceObject = document.getElementById('sumValue');

		var priceDdl = document.getElementById('edu-pricename');
		if(priceDdl !== null) {
			var selected = priceDdl.selectedOptions[0];
			var ppp = 0.0;
			if(selected !== null) {
				ppp = parseFloat(selected.attributes["data-price"].value);
			}
			if(discountPerParticipant !== undefined && discountPerParticipant > 0) {
				var disc = discountPerParticipant * ppp;
				pricePerParticipant = ppp - disc;
			} else {
				pricePerParticipant = ppp;
			}
		}

		if(priceObject && pricePerParticipant !== undefined && currency != '') {

			var newPrice = 0.0;
			var participantPriceNames = document.querySelectorAll('.participantItem:not(.template) .participantPriceName');
			if(participantPriceNames.length > 0) {
				var participants = eduBookingView.CurrentParticipants;
				for(var i = 0; i < participants; i++) {
					if(discountPerParticipant !== undefined && discountPerParticipant > 0) {
						var lpr = parseFloat(participantPriceNames[i].selectedOptions[0].attributes['data-price'].value);
						var disc = discountPerParticipant * lpr;
						newPrice += lpr - disc;
					} else {
						newPrice += parseFloat(participantPriceNames[i].selectedOptions[0].attributes['data-price'].value);
					}
				}
			} else {
				newPrice = (eduBookingView.CurrentParticipants * pricePerParticipant);
			}
			if(!isNaN(questionPrice)) {
				newPrice += questionPrice;
			}

			var subEventPrices = document.querySelectorAll('.eduadmin .participantItem:not(.template):not(.contactPerson) input.subEventCheckBox:checked');
			if(subEventPrices.length > 0) {
				for(var i = 0; i < subEventPrices.length; i++) {
					newPrice += parseFloat(subEventPrices[i].attributes['data-price'].value);
				}
			}

			if(eduBookingView.SingleParticipant || (contactParticipant && contactParticipant.checked)) {
				var subEventPrices = document.querySelectorAll('.eduadmin .participantItem.contactPerson:not(.template) input.subEventCheckBox:checked');
				if(subEventPrices.length > 0) {
					for(var i = 0; i < subEventPrices.length; i++) {
						newPrice += parseFloat(subEventPrices[i].attributes['data-price'].value);
					}
				}
			}

			if(totalPriceDiscountPercent != 0 || eduBookingView.DiscountPercent != 0) {
				var disc = ((totalPriceDiscountPercent + eduBookingView.DiscountPercent) / 100) * newPrice;
				newPrice = newPrice - disc;
			}

			priceObject.innerHTML = numberWithSeparator(newPrice, ' ') + ' ' + currency + ' ' + vatText;
			//priceObject.textContent = numberWithSeparator(newPrice, ' ') + ' ' + currency + ' ' + vatText;
		}

	},
	UpdateInvoiceCustomer: function() {
		var invoiceView = document.getElementById('invoiceView');
		if(invoiceView) {
			invoiceView.style.display = invoiceView.style.display == 'block' ? 'none' : 'block';
		}
	},
	ContactAsParticipant: function() {
		var contactParticipant = document.getElementById('contactIsAlsoParticipant');
		var contact = 0;
		if(contactParticipant) {
			if(contactParticipant.checked) {
				contact = 1;
			} else {
				contact = 0;
			}
		}
		var contactParticipantItem = document.getElementById('contactPersonParticipant');
		if(contactParticipantItem) {
			contactParticipantItem.style.display = contact == 1 ? 'block' : 'none';

			var cFirstName = document.getElementById('edu-contactFirstName').value;
			var cLastName = document.getElementById('edu-contactLastName').value;
			var cEmail = document.getElementById('edu-contactEmail').value;
			var cPhone = document.getElementById('edu-contactPhone').value;
			var cMobile = document.getElementById('edu-contactMobile').value;

			document.querySelector('.contactFirstName').value = cFirstName;
			document.querySelector('.contactLastName').value = cLastName;
			document.querySelector('.contactEmail').value = cEmail;
			document.querySelector('.contactPhone').value = cPhone;
			document.querySelector('.contactMobile').value = cMobile;
			var tCivReg = document.querySelector('.contactCivReg')
			if(tCivReg) {
				var cCivReg = document.getElementById('edu-contactCivReg').value;
				tCivReg.value = cCivReg;
			}

			if(contact == 1 && !this.AddedContactPerson) {
				var freeParticipant = document.querySelector('.eduadmin .participantItem:not(.template):not(.contactPerson)');
				if(freeParticipant) {
					var freeFirstName = freeParticipant.querySelector('.participantFirstName');
					if(freeFirstName) {
						if(freeFirstName.value === '') {
							var removeButton = freeParticipant.querySelector('.removeParticipant');
							var participantHolder = document.getElementById('edu-participantHolder');
							participantHolder.removeChild(removeButton.parentNode.parentNode);
						}
					}
				}
				this.AddedContactPerson = true;
			}
		}
	},
	AddedContactPerson: false,
	ValidateDiscountCode: function() {
		edu.apiclient.CheckCouponCode(
			jQuery('#edu-discountCode').val(),
			jQuery('.validateDiscount').data('objectid'),
			jQuery('.validateDiscount').data('categoryid'),
			function(data) {
				if(data) {
					jQuery('#edu-discountCodeID').val(data.CouponID);
					eduBookingView.DiscountPercent = data.DiscountPercent;
					eduBookingView.UpdatePrice();
				} else {
					// Invalid code
					var codeWarning = document.getElementById('edu-warning-discount');
					if(codeWarning) {
						codeWarning.style.display = 'block';
						setTimeout(function() {
							var codeWarning = document.getElementById('edu-warning-discount');
							codeWarning.style.display = '';
						}, 5000);
					}
				}
			}
		);
	},
	CheckValidation: function() {
		var terms = document.getElementById('confirmTerms');
		if(terms) {
			if(!terms.checked) {
				var termWarning = document.getElementById('edu-warning-terms');
				if(termWarning) {
					termWarning.style.display = 'block';
					setTimeout(function() {
						var termWarning = document.getElementById('edu-warning-terms');
						termWarning.style.display = '';
					}, 5000);
				}
				return false;
			}
		}

		var participants = document.querySelectorAll('.eduadmin .participantItem:not(.template):not(.contactPerson)');
		var requiredFieldsToCreateParticipants = [
			'participantFirstName[]',
			'participantCivReg[]'
		];

		var contactParticipant = document.getElementById('contactIsAlsoParticipant');
		var contact = 0;
		if(contactParticipant) {
			if(contactParticipant.checked) {
				contact = 1;
			} else {
				contact = 0;
			}
		}

		if(eduBookingView.SingleParticipant)
			contact = 1;

		if(participants.length + contact == 0) {
			var noPartWarning = document.getElementById('edu-warning-no-participants');
			if(noPartWarning) {
				noPartWarning.style.display = 'block';
				setTimeout(function() {
					var noPartWarning = document.getElementById('edu-warning-no-participants');
					noPartWarning.style.display = '';
				}, 5000);
			}
			return false;
		}

		for(var i = 0; i < participants.length; i++) {
			var participant = participants[i];
			var fields = participant.querySelectorAll('input');
			for(var f = 0; f < fields.length; f++) {
				if(requiredFieldsToCreateParticipants.indexOf(fields[f].name) >= 0) {

					if(fields[f].value.replace(/ /i, '') == '') {
						/* Show missing participant-name warning */

						var partWarning = document.getElementById('edu-warning-missing-participants');
						if(partWarning) {
							partWarning.style.display = 'block';
							setTimeout(function() {
								var partWarning = document.getElementById('edu-warning-missing-participants');
								partWarning.style.display = '';
							}, 5000);
						}
						return false;
					}
				}
			}
		}

		return true;
	}
};

var eduDetailView = {
	ShowAllEvents: function(filter, me) {
		me.parentNode.parentNode.removeChild(me.parentNode);
		jQuery('.showMoreHidden[data-groupid="' + filter + '"]').slideDown();
	}
};

function numberWithSeparator(x, sep) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, sep);
}

var oldonload = window.onload;
window.onload = function(e) {
	if(oldonload)
		oldonload();
};
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function (event) {
        var trgt = event.target;
        if ( trgt.classList.contains('confirm') ) {
            let approv = confirm(trgt.dataset.confirm);
            if ( ! approv ) {
                event.preventDefault();
            }
        }
        if ( trgt.parentElement.classList.contains('header__menu-switcher') ) {
            document.querySelector('.header__menu').classList.toggle('hide');
        }
        if ( trgt.parentElement.classList.contains('menu__item_hide-trash') ) {
            document.querySelector('body').classList.remove('page_trash');
        }
        if ( trgt.parentElement.classList.contains('menu__item_show-trash') ) {
            document.querySelector('body').classList.add('page_trash');
        }
    });
});
function getZip(elem) {
    let link = elem.dataset.link;
    let origText = elem.textContent;
    elem.textContent = 'Пожалуйста, подождите';
    let timerId = setInterval(function() {
        elem.textContent = elem.textContent + '.';
    }, 1000);

    fetch(link)
	.then(
		response => {
            clearInterval(timerId);
            elem.textContent = origText;
			if (response.ok) {
				window.location.href = elem.dataset.link;
			} else {
				throw new Error('Ошибка на сайте. Попробуйте позднее');
			}
		},
	).catch(
		error => {
            elem.insertAdjacentText('afterEnd', error);
		}
	);
}
function processXml(fileIndex = 0, startPos = 0) {
	let params = new URLSearchParams();
	
	params.set('fileindex', fileIndex);
	params.set('startpos', startPos);

    let block = document.querySelector('.process__status');
    let links = document.querySelector('.process__links');
    let btns = document.querySelector('.process__btns');
    
    fetch('/', {
		method: 'post',
		body: params,
	}).then(
        response => {
            if (response.ok) {
                return response.text();
            } else {
				throw new Error('Ошибка на сайте. Попробуйте позднее');
            }
        },
    ).then(
        text => {
            let obj = JSON.parse(text);
            let p = document.createElement('p');
            p.classList.add('process__bar');
            let perc = 0;
            if (obj.files !== false) {
                if (obj.startpos !== false ) {
                    processXml(fileIndex, obj.startpos);
                    perc = (obj.startpos / obj.files[fileIndex].size * 100).toFixed() + '%';
                } else {
                    processXml(fileIndex + 1, 0);
                    perc = '100%';
                }
                p.style.width = perc;
                block.innerHTML = p.outerHTML;
                block.insertAdjacentText('afterBegin', obj.files[fileIndex].name + ': ' + perc);
            } else {
                btns.classList.add('hide');
                links.classList.remove('hide');
            }
        }
    ).catch(
        error => {
            block.insertAdjacentText('afterBegin', error);
        }
    );
}
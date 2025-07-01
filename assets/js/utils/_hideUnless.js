export default function hideUnless() {
    let hideUnlessElements = document.querySelectorAll('[data-hide-unless]');
    if (!hideUnlessElements.length) {
        return;
    }

    let hideUnlessAttributes = Object.values(hideUnlessElements).map((element) => {
        let condition = getConditionObject(element.getAttribute('data-hide-unless'));
        return condition?.name;
    });
    hideUnlessAttributes = hideUnlessAttributes.filter(
        (attribute, index, array) => attribute && array.indexOf(attribute) !== index
    );

    hideUnlessAttributes.forEach((id) => {
        document.querySelector(`[data-hide-unless-${id}]`).addEventListener('change', () => {
            let elements = Object.values(hideUnlessElements).filter((element) => {
                let condition = getConditionObject(element.getAttribute('data-hide-unless'));
                return condition?.name === id;
            })

            checkHiddenElements(elements)
        });
    });

    checkHiddenElements(hideUnlessElements)
}

function checkHiddenElements(elements) {
    elements.forEach((element) => {
        let condition = getConditionObject(element.getAttribute('data-hide-unless'));
        if (condition) {
            let input = document.querySelector(`[data-hide-unless-${condition.name}]`);
            if (input.value === condition.value) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    });
}

function getConditionObject(condition) {
    if (!condition) {
        return null;
    }

    let data = condition.split('::');
    return {
        name: data[0],
        value: data[1]
    };
}
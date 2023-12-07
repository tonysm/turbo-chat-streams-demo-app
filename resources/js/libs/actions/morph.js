import Idiomorph from "libs/idiomorph"

export default function morph() {
    const morphStyle = this.getAttribute("morph-style") || "outerHTML"

    this.targetElements.forEach((currentElement) => {
        Idiomorph.morph(currentElement, this.templateContent, {
            morphStyle: morphStyle,
            callbacks: {
                beforeNodeAdded: shouldAddElement,
                beforeNodeMorphed: shouldMorphElement,
                beforeNodeRemoved: shouldRemoveElement,
            },
        })
    })
}

function shouldAddElement(node) {
    return !(node.id && node.hasAttribute("data-turbo-permanent") && document.getElementById(node.id))
}

function shouldMorphElement(oldNode, newNode) {
    if (oldNode instanceof HTMLElement) {
        return !oldNode.hasAttribute("data-turbo-permanent") && !isFrameReloadedWithMorph(oldNode)
    }

    return true
}

function isFrameReloadedWithMorph(element) {
   return element.srd && element.refresh === "morph"
}

function shouldRemoveElement(node) {
    return shouldMorphElement(node)
}

import { Controller } from '@hotwired/stimulus'
import Quill from 'quill'
import htmlEditButton from 'quill-html-edit-button';

Quill.register('modules/htmlEditButton', htmlEditButton);
export default class extends Controller {
    connect() {
        const editorElement = this.element.querySelector('.quill-editor')
        const hiddenInput = this.element.querySelector('textarea, input[type=hidden]')

        this.editor = new Quill(editorElement, {
            theme: 'snow',
            placeholder: 'Saisir le texte ici...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote', 'code-block', 'image'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['clean']
                ],
                htmlEditButton: {}
            }
        })

        // Initialiser avec la valeur existante
        if (hiddenInput.value) {
            this.editor.clipboard.dangerouslyPasteHTML(hiddenInput.value)
            //this.editor.root.innerHTML = hiddenInput.value
        }

        this.editor.on('text-change', () => {
            hiddenInput.value = this.editor.root.innerHTML
        })
    }
}

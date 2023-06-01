let token = $('meta[name="csrf-token"]').attr('content');
ClassicEditor
    .create(document.querySelector('#editor'), {
        ckfinder: {
            uploadUrl: "/admin/upload?_token=" + token
        }
    })
    .then(editor => {
        // Set the height of the editor
        editor.editing.view.change(writer => {
            writer.setStyle(
                'height',
                '200px',
                editor.editing.view.document.getRoot()
            );
        });
    })
    .catch(error => {
        console.error(error);
    });


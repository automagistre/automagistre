import {TextField, TextFieldProps, TextInput, TextInputProps} from 'react-admin'

export const CommentInput = (props: Omit<TextInputProps, 'source'>) => {
    return <TextInput
        source="comment"
        label="Комментарий"
        multiline={true}
        {...props}
    />
}

CommentInput.defaultProps = {
    fullWidth: true,
}

export const CommentField = (props: Omit<TextFieldProps, 'source'>) => {
    return <TextField
        source="comment"
        label="Комментарий"
        {...props}
    />
}

CommentField.defaultProps = {
    label: 'Комментарий',
}

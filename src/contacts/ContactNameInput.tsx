import {TextInput, useGetList} from 'react-admin';
import {useFormState} from "react-final-form";

const ContactNameInput = () => {
    const {values} = useFormState();
    const {data, loading} = useGetList('legal_form');

    if (loading) return <span>Загрузка...</span>;

    if (!values.legal_form) return <span>Выберите правовую форму</span>

    const legalForm = data[values.legal_form]

    if (legalForm.type === 'person') {
        return <>
            <TextInput source="name.lastname" label="Фамилия"/>
            <TextInput source="name.firstname" label="Имя"/>
            <TextInput source="name.middlename" label="Отчество"/>
        </>
    }

    if (legalForm.type === 'organization') {
        return <>
            <TextInput source="name.name" label="Краткое название"/>
            <TextInput source="name.full_name" label="Полное название" fullWidth/>
        </>
    }

    console.error(`Legal form "${values.legal_form}" not found`)

    return <span>Ошибка правовой формы, обратитесь к разработчикам.</span>
};

ContactNameInput.defaultProps = {
    label: "Наименование",
    source: 'name',
};

export default ContactNameInput;

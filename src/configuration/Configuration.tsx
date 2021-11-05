import {useDispatch} from 'react-redux'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import {Button} from '@mui/material'
import {Title} from 'react-admin'
import {makeStyles} from '@mui/styles'
import {changeTheme} from './actions'

const useStyles = makeStyles({
    label: {width: '10em', display: 'inline-block'},
    button: {margin: '1em'},
})

const Configuration = () => {
    const classes = useStyles()
    const dispatch = useDispatch()
    return (
        <Card>
            <Title title="Настройки"/>
            <CardContent>
                <div className={classes.label}>Тема</div>
                <Button
                    variant="contained"
                    className={classes.button}
                    onClick={() => dispatch(changeTheme('light'))}
                >
                    Светлая
                </Button>
                <Button
                    variant="contained"
                    className={classes.button}
                    onClick={() => dispatch(changeTheme('dark'))}
                >
                    Тёмная
                </Button>
            </CardContent>
        </Card>
    )
}

export default Configuration

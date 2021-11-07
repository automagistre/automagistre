export const darkTheme = {
    sidebar: {
        width: 200,
    },
    components: {
        MuiButtonBase: {
            defaultProps: {
                disableRipple: true,
            },
            styleOverrides: {
                root: {
                    '&:hover:active::after': {
                        content: '""',
                        display: 'block',
                        width: '100%',
                        height: '100%',
                        position: 'absolute',
                        top: 0,
                        right: 0,
                        backgroundColor: 'currentColor',
                        opacity: 0.3,
                        borderRadius: 'inherit',
                    },
                },
            },
        }, MuiAppBar: {
            styleOverrides: {
                colorSecondary: {
                    color: '#ffffffb3', backgroundColor: '#616161e6',
                },
            },
        },
    },
    mixins: {},
    palette: {
        text: {
            hint: 'rgba(255, 255, 255, 0.5)',
        },
        mode: 'dark',
        type: 'dark' as 'dark',
        primary: {
            main: '#90caf9',
        },
        secondary: {
            main: '#FBBA72',
        },
    },
}

export const lightTheme = {
    shape: {
        borderRadius: 10,
    },
    sidebar: {
        width: 200,
    },
    components: {
        MuiButtonBase: {
            defaultProps: {
                disableRipple: true,
            },
            styleOverrides: {
                root: {
                    '&:hover:active::after': {
                        content: '""',
                        display: 'block',
                        width: '100%',
                        height: '100%',
                        position: 'absolute',
                        top: 0,
                        right: 0,
                        backgroundColor: 'currentColor',
                        opacity: 0.3,
                        borderRadius: 'inherit',
                    },
                },
            },
        },
        RaMenuItemLink: {
            styleOverrides: {
                root: {
                    borderLeft: '3px solid #fff',
                },
                active: {
                    borderLeft: '3px solid #4f3cc9',
                },
            },
        },
        MuiPaper: {
            styleOverrides: {
                'elevation1': {
                    boxShadow: 'none',
                },
                root: {
                    border: '1px solid #e0e0e3', backgroundClip: 'padding-box',
                },
            },
        },
        MuiButton: {
            styleOverrides: {
                contained: {
                    backgroundColor: '#fff',
                    color: '#4f3cc9',
                    boxShadow: 'none',
                },
            },
        },
        MuiAppBar: {
            styleOverrides: {
                colorSecondary: {
                    color: '#808080', backgroundColor: '#fff',
                },
            },
        },
        MuiLinearProgress: {
            styleOverrides: {
                colorPrimary: {
                    backgroundColor: '#f5f5f5',
                },
                barColorPrimary: {
                    backgroundColor: '#d7d7d7',
                },
            },
        },
        MuiFilledInput: {
            styleOverrides: {
                root: {
                    backgroundColor: 'rgba(0, 0, 0, 0.04)',
                    '&$disabled': {
                        backgroundColor: 'rgba(0, 0, 0, 0.04)',
                    },
                },
            },
        },
        MuiSnackbarContent: {
            styleOverrides: {
                root: {
                    border: 'none',
                },
            },
        },
    },
    mixins: {},
    palette: {
        text: {
            hint: 'rgba(0, 0, 0, 0.38)',
        },
        mode: 'light',
        type: 'light' as 'light',
        primary: {
            main: '#4f3cc9',
        },
        secondary: {
            light: '#5f5fc4', main: '#283593', dark: '#001064', contrastText: '#fff',
        },
        background: {
            default: '#fcfcfe',
        },
    },
}

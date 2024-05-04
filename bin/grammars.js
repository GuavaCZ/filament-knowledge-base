const args = process.argv.slice(2);

async function main(args) {
    const {grammars} = await import('tm-grammars');
    const language = args[0];
    const meta = grammars
        .find(obj => obj.name === language ||
            (obj.hasOwnProperty('aliases') && obj.aliases.includes(language))
        );
    process.stdout.write(meta ? meta.displayName : 'Text');
}


main(args)

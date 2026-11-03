import DemoCard from '@/components/demo/demo-card';
import { type DemoFeatures } from '@/types';

export default function EnvironmentCard({ environment }: { environment: DemoFeatures['environment'] }) {
    const rows: [string, string | null][] = [
        ['APP_ENV', environment.app_env],
        ['Arco environment', environment.arco_env],
        ['PHP', environment.php],
        ['Region', environment.region],
    ];

    return (
        <DemoCard title="Environment" description="Where this copy of the app is running." configured>
            <dl className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
                {rows.map(([label, value]) => (
                    <div key={label} className="contents">
                        <dt className="text-muted-foreground">{label}</dt>
                        <dd>{value ?? 'not set'}</dd>
                    </div>
                ))}
            </dl>
        </DemoCard>
    );
}

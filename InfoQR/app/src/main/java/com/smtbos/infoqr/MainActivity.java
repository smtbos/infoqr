package com.smtbos.infoqr;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.method.ScrollingMovementMethod;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

public class MainActivity extends AppCompatActivity {

    private SharedPreferences sharedPreferences;
    private SharedPreferences.Editor editor;

    private TextView lbl_heading, lbl_info;
    private Button btn_scan_qr, btn_logout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        sharedPreferences = getSharedPreferences("InfoQR", MODE_PRIVATE);
        editor = sharedPreferences.edit();

        this.verifyLogin();

        lbl_heading = (TextView) findViewById(R.id.lbl_heading);
        lbl_info = (TextView) findViewById(R.id.lbl_info);
        btn_scan_qr = (Button) findViewById(R.id.btn_scan_qr);
        btn_logout = (Button) findViewById(R.id.btn_login);

        lbl_info.setMovementMethod(new ScrollingMovementMethod());

        btn_scan_qr.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(MainActivity.this, ScanQrActivity.class);
                startActivity(i);
            }
        });

        btn_logout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                editor.remove("u_id");
                editor.commit();
                Intent i = new Intent(MainActivity.this, LoginActivity.class);
                startActivity(i);
            }
        });

        Bundle bundle = getIntent().getExtras();
        if (bundle != null) {
            if (bundle.getString("information") != null) {
                lbl_info.setText(bundle.getString("information"));
                lbl_heading.setVisibility(View.VISIBLE);
                lbl_info.setVisibility(View.VISIBLE);
            }
        }
    }

    private void verifyLogin() {
        int u_id = 0;
        u_id = sharedPreferences.getInt("u_id", 0);
        if (u_id == 0) {
            Intent i = new Intent(MainActivity.this, LoginActivity.class);
            startActivity(i);
        }
    }
}